<?php

namespace Weather\WeatherStack\Infrastructure\Command;

use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryStore;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherFactory;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherRequest;
use AccidentPrediction\SafeFunction;
use Weather\WeatherStack\HistoricalWeatherApi;
use Weather\WeatherStack\Infrastructure\Command\Tools\ColumnNameFactory;
use Weather\WeatherStack\Infrastructure\Command\Tools\ConvertCsvToLocationTimeDTO;
use Weather\WeatherStack\Infrastructure\Command\Tools\CsvParser;
use Weather\WeatherStack\Infrastructure\Command\Tools\OutputCsvPresenter;
use Weather\WeatherStack\Infrastructure\Command\Tools\SearchHistoricalWeatherDecorated;
use Weather\WeatherStack\Infrastructure\Command\Tools\SearchHistoricalWeatherDecoratedFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\getcwd;
use function SafePHP\intval;
use function SafePHP\strval;

#[AsCommand(
    name: 'ap:weather:insert-historical',
    description: 'Insert historical weather data inside a csv file that contains points',
    hidden: false,
)]
class InsertHistoricalCommand extends Command
{
    public const CSV_FILENAME_ARG_NAME = 'csvFileName';
    public const CSV_OUTPUT_FILENAME_ARG_NAME = 'outputFilename';
    public const TIMES = 'times';

    private string $csvFileName = "";

    private string $csvOutputFileName = "";

    private string $workDir = "";

    private int $numberOfLineToCompute = -1;

    public function __construct(
        string $name = null,
        private ?HistoricalWeatherApi $api = null,
        ?string $workDir = null,
        private SearchHistoricalWeatherFactory $factory = new SearchHistoricalWeatherDecoratedFactory()
    ) {
        $this->workDir = $workDir ?: getcwd();

        parent::__construct($name); // appel after init because it calls configure()
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::CSV_FILENAME_ARG_NAME,
            InputArgument::REQUIRED,
            'CSV file containing at leat latitude, longitude and historical date'
        );

        $this->addArgument(
            self::CSV_OUTPUT_FILENAME_ARG_NAME,
            InputArgument::OPTIONAL,
            'ouput file name',
            'result.csv'
        );

        $this->addArgument(
            self::TIMES,
            InputArgument::OPTIONAL,
            'Times of request : t0 : the locationtime, t1,t2,... the locationtime plus 1,2... hours,".
            " m1,m2... the locationtime minus 1,2... hour',
            't0,m3'
        );

        $this->addOption(
            "numberOfLineToCompute",
            mode: InputOption::VALUE_OPTIONAL,
            description: "Number of lines to compute",
            default:"-1"
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Inserting historical weather data...',
            '------------------------------------',
        ]);

        $output->writeln("API : " . substr($this->getApiKey(), 0, 3) . "(...)");

        $this->csvFileName = strval($input->getArgument(self::CSV_FILENAME_ARG_NAME));

        $this->csvOutputFileName = strval($input->getArgument(self::CSV_OUTPUT_FILENAME_ARG_NAME));

        $this->numberOfLineToCompute = intval($input->getOption("numberOfLineToCompute"));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = hrtime(true);

        $csvContent =  $this->getCsvContentCorrectlySized();

        $lineToCompute = substr_count($csvContent, "\n") - 1;
        $output->writeln(sprintf("Computing %s lines...", $lineToCompute));

        $progressBar = new ProgressBar($output, $lineToCompute);
        $progressBar->start();
        $response = $this->executeWeatherInsertion($csvContent, $progressBar);

        $end = hrtime(true);
        $eta = round(($end - $start) / 1e+9, 1);

        $output->writeln("");
        $output->writeln(sprintf("Script lasted : %s seconds", $eta));
        $output->writeln(sprintf("Api requested : %s times.", $response->apiQueryCount));
        $output->writeln(sprintf("Repository requested : %s times.", $response->storedQueryCount));
        $output->writeln(sprintf("Number of lines computed : %s", $response->storedQueryCount));

        $output->write(sprintf("Recording file : %s ... ", $this->getCsvResultFileName()));
        $this->recordCsvResultFile($response->csvString);

        $output->writeln("Finished!");

        return Command::SUCCESS;
    }

    private function executeWeatherInsertion(string $csvContent, ProgressBar $progressBar): \stdClass
    {
        $service = $this->prepareService($csvContent);
        $service->setProgressBar($progressBar);

        $request = $this->prepareRequest($csvContent);
        $service->execute($request);

        return (object)$service->getPresenter()->read();
    }

    private function getCsvContentCorrectlySized(): string
    {
        $csvContent = file_get_contents($this->csvFileName);
        $csvContent = CsvParser::trunquate($csvContent, $this->numberOfLineToCompute);
        return $csvContent;
    }

    private function prepareService(string $csvContent): SearchHistoricalWeatherDecorated
    {
        $api = $this->api == null ? HistoricalWeatherApi::create($this->getApiKey()) : $this->api;

        $colNames = ColumnNameFactory::create("", "");
        $presenter = new OutputCsvPresenter($csvContent, $colNames);

        $repository = new HistoricalDayRepositoryStore();

        /** @var SearchHistoricalWeatherDecorated */
        return $this->factory->create($api, $repository, $presenter);
    }

    private function prepareRequest(string $csvContent): SearchHistoricalWeatherRequest
    {
        $locationTimes = ConvertCsvToLocationTimeDTO::convert($csvContent);
        return new SearchHistoricalWeatherRequest($locationTimes, [-3,0]);
    }

    private function getApiKey(): string
    {
        return isset($_ENV['WEATHERSTACK_API']) ? $_ENV['WEATHERSTACK_API'] : getenv('WEATHERSTACK_API');
    }

    private function recordCsvResultFile(string $csv): void
    {
        file_put_contents($this->getCsvResultFileName(), $csv);
    }

    private function getCsvResultFileName(): string
    {
        return $this->workDir . '/' . $this->csvOutputFileName;
    }
}

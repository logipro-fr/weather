<?php

namespace Weather\WeatherStack\Infrastructure\Command;

use Weather\PresenterFile;
use Weather\WeatherStack\Application\Service\GetCurrentWeather\GetCurrentWeather;
use Weather\WeatherStack\Application\Service\GetCurrentWeather\GetCurrentWeatherRequest;
use Weather\WeatherStack\Application\Service\WeatherAPIInterface;
use Weather\WeatherStack\WeatherStackApi;
use Weather\WeatherStack\WeatherStackTools;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ap:weather:query',
    description: 'Weather query',
    hidden: false,
)]
class WeatherQueryCommand extends Command
{
    public function __construct(string $name = null, private ?WeatherAPIInterface $api = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $apiKey = $_ENV['WEATHERSTACK_API'];

        $api = $this->api == null ? WeatherStackApi::create($apiKey) : $this->api;
        $presenter = new PresenterFile();
        $service = new GetCurrentWeather($presenter, $api);

        $hotpoints = WeatherStackTools::get2500Hotpoint();
        $request = new GetCurrentWeatherRequest($hotpoints);

        $service->execute($request);
        $response = $presenter->read();
        return Command::SUCCESS;
    }
}

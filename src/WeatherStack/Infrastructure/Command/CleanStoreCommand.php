<?php

namespace Weather\WeatherStack\Infrastructure\Command;

use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryStore;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\Application\Service\CleanHistoricalDay;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ap:weather:clean-store',
    description: 'clean store of bad values',
    hidden: false,
)]
class CleanStoreCommand extends Command
{
    public function __construct(
        private CleanHistoricalDay $service
    ) {
        parent::__construct("clean store");
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Find and clean bad weather query...',
            '============',
            '',
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->service->execute();

        $output->writeln("Cleaned!");

        return Command::SUCCESS;
    }
}

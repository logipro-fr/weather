<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command;

use Weather\WeatherStack\Infrastructure\Command\WeatherQueryCommand;
use PHPUnit\Framework\TestCase;
use Weather\WeatherStack\WeatherStackApi;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherQueryCommandTest extends TestCase
{
    protected function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('VFSDir'));
    }


    public function testExecute(): void
    {
        $_ENV['WEATHERSTACK_API'] = "fakeAPIKEY";

        $apiMock = $this->createMock(WeatherStackApi::class);

        $command = new WeatherQueryCommand('ap:weather:query', $apiMock);

        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);

        $result = $command->run($inputMock, $outputMock);

        $this->assertEquals(Command::SUCCESS, $result);
    }
}

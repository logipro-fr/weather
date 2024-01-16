<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command;

use Weather\WeatherStack\Application\Service\CleanHistoricalDay;
use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CleanStoreCommandTest extends KernelTestCase
{
    private Application $application;

    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();

        $service = $this->getMockBuilder(CleanHistoricalDay::class)
            ->setConstructorArgs([new HistoricalDayRepositoryInMemory()])
            ->getMock();
        $service
            ->expects($this->once())
            ->method("execute");

        $kernel->getContainer()->set(CleanHistoricalDay::class, $service);

        $this->application = new Application($kernel);

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('VFSDir'));
    }

    public function testExecute(): void
    {
        $command = $this->application->find("ap:weather:clean-store");
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Find and clean bad weather query...', $output);
        $this->assertStringContainsString('Cleaned!', $output);
    }
}

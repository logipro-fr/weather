<?php

namespace Weather\Tests;

use Weather\WeatherStack\Application\Service\GetCurrentWeather\GetCurrentWeatherResponse;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Safe\DateTimeImmutable;
use Weather\WeatherStack\PresenterFile;

class PresenterFileTest extends TestCase
{
    protected function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('VFSDir'));
    }

    public function testWrite(): void
    {
        $presenter = new PresenterFile(new DateTimeImmutable("2023-06-27 12:13:14"));
        $hotpoints = [
            "1,1" => "{}"
        ];
        $fakeReport = new \stdClass();
        $response = new GetCurrentWeatherResponse($fakeReport, $hotpoints);
        $presenter->write($response);

        $filename = $presenter->getFilenameFullPath();
        $this->assertFileExists($filename);

        $this->assertEquals($response, $presenter->read());
    }

    public function testGetFilenameFullPath(): void
    {
        $presenter = new PresenterFile(new DateTimeImmutable("2023-06-27 12:13"));
        $response = new GetCurrentWeatherResponse(new \stdClass(), []);
        $presenter->write($response);
        $name = $presenter->getFilenameFullPath();
        $this->assertEquals("vfs://VFSDir/2023/06/27/2023-06-27-12-13.json", $name);
    }
}

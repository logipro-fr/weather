<?php

namespace Weather\Tests\Application\ImportLegacy;

use PHPUnit\Framework\TestCase;
use Weather\Application\ImportLegacy\ImportLegacyResponse;
use Weather\Application\ImportLegacy\ImportLegacySQL;
use Weather\Application\ImportLegacy\ImportLegacySQLRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class ImportLegacySQLTest extends TestCase
{
    public function testExecute(): void
    {
        $presenter = new PresenterObject();
        $repository = new WeatherInfoRepositoryInMemory();
        $service = new ImportLegacySQL($presenter, $repository);

        $request = new ImportLegacySQLRequest(
            "mysql:host=weather-mariadb:3306;dbname=weather",
            "currentweathers",
            "weather",
            "weather"
        );
        $service->execute($request);

        /** @var ImportLegacyResponse $response */
        $response = $service->getPresenter()->read();
        /** @var array<string,int> $responseData */
        $responseData = $response->getData();
        $this->assertEquals(1000, $responseData["size"]);
        $this->assertFalse($repository->findById(new WeatherInfoId("64edae4186d23"))->isHistorical());
    }
}

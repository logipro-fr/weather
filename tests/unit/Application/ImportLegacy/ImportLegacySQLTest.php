<?php

namespace Weather\Tests\Application\ImportLegacy;

use PHPUnit\Framework\TestCase;
use Weather\Application\ImportLegacy\ImportLegacySQL;
use Weather\Application\ImportLegacy\ImportLegacySQLRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

use function PHPUnit\Framework\assertEquals;

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
        $response = $presenter->read();
        assertEquals(1000, $response->getData()["size"]);
    }
}

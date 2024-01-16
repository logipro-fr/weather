<?php

namespace Weather\Tests\WeatherStack\Application\Service\SearchHistoricalWeather;

use Weather\Share\Domain\Point;
use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeather;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherRequest;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherResponse;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\LocationTimeDTO;
use Weather\Tests\WeatherStack\TestTools\FakeHistorical;
use Weather\WeatherStack\HistoricalWeatherApi;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class SearchHistoricalWeatherTest extends TestCase
{
    private SearchHistoricalWeather $service;

    private HistoricalDayRepositoryInMemory $repository;

    protected function setUp(): void
    {
        $fake = new FakeHistorical();
        $httpClient = $fake->getMockHttpClient();
        $api = HistoricalWeatherApi::create("1234", $httpClient);
        $this->repository = new HistoricalDayRepositoryInMemory();
        $this->service = new SearchHistoricalWeather($api, $this->repository);
    }

    public function testExecuteSimple(): void
    {
        $request = $this->makeSearchWeatherFeaturesRequest("2023-01-25 1");

        $this->service->execute($request);

        $response = $this->service->getPresenter()->read();
        $this->assertInstanceOf(SearchHistoricalWeatherResponse::class, $response);
        /** @var SearchHistoricalWeatherResponse $response  */
        $this->assertEquals(1, count($response->wishedHourArrays));
        $weather = $response->wishedHourArrays[0][0];
        $this->assertEquals("2023-01-25 01:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));

        $this->assertEquals(-3, $weather->getTemperature());

        $this->assertEquals(2, $response->apiQueryCount);
        $this->assertEquals(0, $response->storedQueryCount);
    }

    /**
     * @param array<int> $otherHours
     */
    private function makeSearchWeatherFeaturesRequest(
        string $date,
        array $otherHours = [ 0 ]
    ): SearchHistoricalWeatherRequest {
        return new SearchHistoricalWeatherRequest(
            [
                new LocationTimeDTO(45.033, 3.883, $date),
                new LocationTimeDTO(44.039, 4.348, $date)
            ],
            $otherHours
        );
    }

    public function testUseRepository(): void
    {
        $historicalDate = DateTimeImmutable::createFromFormat("Y-m-d", "2023-01-25");

        $request = $this->makeSearchWeatherFeaturesRequest($historicalDate->format("Y-m-d H"));

        // locations used to construct id are those inside the example
        $id1 = new HistoricalDayId(new Point(45.033, 3.883), $historicalDate);
        $id2 = new HistoricalDayId(new Point(44.039, 4.348), $historicalDate);

        $this->service->execute($request);

        $this->assertTrue($this->repository->existById($id1));
        $this->assertTrue($this->repository->existById($id2));

        $wf = $this->repository->findById($id1);
        $this->assertEquals(-3, $wf->makeHistoricalHour(1)->getTemperature());

        $wf = $this->repository->findById($id2);
        $this->assertEquals(5, $wf->makeHistoricalHour(11)->getTemperature());
    }

    public function testUseRepositoryStoreFindInsteadOfApiQuery(): void
    {
        $historicalDate = DateTimeImmutable::createFromFormat("Y-m-d", "2023-01-25");

        $request = $this->makeSearchWeatherFeaturesRequest($historicalDate->format("Y-m-d H"));

        $this->service->execute($request);
        $this->service->execute($request);

        /** @var SearchHistoricalWeatherResponse $response  */
        $response = $this->service->getPresenter()->read();
        $this->assertEquals(0, $response->apiQueryCount);
        $this->assertEquals(2, $response->storedQueryCount);
    }

    public function testApiQueryFailure(): void
    {
        $historicalDate = DateTimeImmutable::createFromFormat("Y-m-d H", "2000-01-25 17");

        $request = $this->makeSearchWeatherFeaturesRequest($historicalDate->format("Y-m-d H"));

        $this->service->execute($request);

        /** @var SearchHistoricalWeatherResponse $response  */
        $response = $this->service->getPresenter()->read();
        $this->assertEquals(2, count($response->failures));

        $this->assertEquals(45.033, $response->failures[0]->latitude);
        $this->assertEquals(3.883, $response->failures[0]->longitude);
        $this->assertEquals("2000-01-25 17", $response->failures[0]->time);

        $this->assertEquals(44.039, $response->failures[1]->latitude);
        $this->assertEquals(4.348, $response->failures[1]->longitude);
        $this->assertEquals("2000-01-25 17", $response->failures[1]->time);
    }

    public function testExecuteWithOtherHours(): void
    {
        $request = $this->makeSearchWeatherFeaturesRequest("2023-01-26 1", [0, -3]);

        $this->service->execute($request);

        $response = $this->service->getPresenter()->read();
        $this->assertInstanceOf(SearchHistoricalWeatherResponse::class, $response);
        /** @var SearchHistoricalWeatherResponse $response  */
        $this->assertEquals(2, count($response->wishedHourArrays));
        $this->assertEquals(4, $response->apiQueryCount);
        $this->assertEquals(0, $response->storedQueryCount);

        $espalyWeather = $response->wishedHourArrays[0][0];
        $this->assertEquals("2023-01-26 01:00", $espalyWeather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals(-1, $espalyWeather->getTemperature());
        $this->assertEquals(301, $espalyWeather->getWindDegree());

        $serviersWeather = $response->wishedHourArrays[0][1];
        $this->assertEquals("2023-01-26 01:00", $serviersWeather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals(0, $serviersWeather->getTemperature());
        $this->assertEquals(356, $serviersWeather->getWindDegree());


        $minus3EspalyWeather = $response->wishedHourArrays[-3][0];
        $this->assertEquals("2023-01-25 22:00", $minus3EspalyWeather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals(-1, $minus3EspalyWeather->getTemperature());
        $this->assertEquals(320, $minus3EspalyWeather->getWindDegree());

        $minus3ServiersWeather = $response->wishedHourArrays[-3][1];
        $this->assertEquals("2023-01-25 22:00", $minus3ServiersWeather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals(1, $minus3ServiersWeather->getTemperature());
        $this->assertEquals(356, $minus3ServiersWeather->getWindDegree());
    }
}

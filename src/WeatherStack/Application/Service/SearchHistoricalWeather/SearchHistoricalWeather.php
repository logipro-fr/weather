<?php

namespace Weather\WeatherStack\Application\Service\SearchHistoricalWeather;

use Weather\Application\Share\PresenterInterface;
use Weather\Application\Share\PresenterObject;
use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Exceptions\APIErrorException;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\Domain\Model\HistoricalHour;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use Weather\WeatherStack\HistoricalWeatherApi;
use Safe\DateTimeImmutable;

class SearchHistoricalWeather implements SearchHistoricalWeatherAbstractService
{
    private int $apiQueryCount = 0;
    private int $storedQueryCount = 0;

    /** @var array<LocationTimeDTO>  */
    private array $apiQueryFailures = [];

    public function __construct(
        private HistoricalWeatherApi $api,
        private HistoricalDayRepositoryInterface $repository,
        private PresenterInterface $presenter = new PresenterObject()
    ) {
    }

    public function getPresenter(): PresenterInterface
    {
        return $this->presenter;
    }

    public function execute(SearchHistoricalWeatherRequest $request): void
    {
        /** @var array<int,array<HistoricalHour>> */
        $weatherOtherHours = [];
        foreach ($request->otherHours as $hour) {
            $weatherOtherHours[$hour] = [];
        }

        $this->apiQueryCount = 0;
        $this->storedQueryCount = 0;
        foreach ($request->locationTimes as $pointTime) {
            $this->hookLoop($pointTime);
            $this->fillALineOfLocationTimeWithAllHistoricalHoursNeed(
                $weatherOtherHours,
                new LocationTime(
                    $pointTime->latitude,
                    $pointTime->longitude,
                    DateTimeImmutable::createFromFormat("Y-m-d H", $pointTime->time)
                )
            );
        }

        $response = new SearchHistoricalWeatherResponse(
            $weatherOtherHours,
            $this->apiQueryCount,
            $this->storedQueryCount,
            $this->apiQueryFailures
        );
        $this->presenter->write($response);
    }

    /**
     *
     * @param LocationTimeDTO $locationTime implement this method in a child class to decorated
     * @return void
     */
    protected function hookLoop(
        LocationTimeDTO $locationTime,
    ): void {
    }

    /**
     * @param array<int,array<HistoricalHour>> $weatherOtherHours
     */
    private function fillALineOfLocationTimeWithAllHistoricalHoursNeed(
        array &$weatherOtherHours,
        LocationTime $locationTime
    ): void {
        foreach ($weatherOtherHours as $hour => $value) {
            $otherDate = $locationTime->getTime()->modify($hour . ' hours');
            $otherLocationTime = new LocationTime(
                $locationTime->getLatitude(),
                $locationTime->getLongitude(),
                $otherDate
            );
            $this->bestWayToAddHistorical($otherLocationTime, $weatherOtherHours[$hour]);
        }
    }

    /**
     * @param array<HistoricalHour> $weatherHour
     */
    protected function bestWayToAddHistorical(
        LocationTime $locationTime,
        array &$weatherHour
    ): void {
        $id = new HistoricalHourId($locationTime);
        if ($this->repository->existdByHistoricalHourId($id)) {
            $historicalDay = $this->repository->findByHistoricalHourId($id);
            $this->storedQueryCount++;
            $weatherHour[] = $this->makeHistoricalHour($historicalDay, $locationTime);
            return;
        }
        $this->tryToRequestApi($locationTime, $weatherHour);
    }

    /**
     * @param array<HistoricalHour> $weatherHour
     */
    private function tryToRequestApi(
        LocationTime $locationTime,
        array &$weatherHour
    ): void {
        try {
            $content = $this->api->getHistoricalWeather($locationTime);
            $this->repository->add($content);
            $this->apiQueryCount++;
            $weatherHour[] = $this->makeHistoricalHour($content, $locationTime);
        } catch (APIErrorException $e) {
            $this->apiQueryFailures[] = new LocationTimeDTO(
                $locationTime->getLatitude(),
                $locationTime->getLongitude(),
                $locationTime->getTime()->format('Y-m-d H')
            );
        }
    }

    private function makeHistoricalHour(HistoricalDay $historicalDay, LocationTime $locationTime): HistoricalHour
    {
        return $historicalDay->makeHistoricalHour(intval($locationTime->getTime()->format("H")));
    }
}

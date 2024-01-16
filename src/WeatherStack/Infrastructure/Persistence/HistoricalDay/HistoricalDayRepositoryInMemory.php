<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\WeatherStack\Domain\Model\Exceptions\HistoricalDayNotFoundException;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use JsonException;

use function Safe\json_decode;

class HistoricalDayRepositoryInMemory implements HistoricalDayRepositoryInterface
{
    /** @var array<HistoricalDay> */
    public array $days = [];

    public function add(HistoricalDay $weatherHistoricalFeatures): void
    {
        $this->days[] = $weatherHistoricalFeatures;
    }

    public function existById(HistoricalDayId $id): bool
    {
        foreach ($this->days as $historicalDay) {
            if ($historicalDay->getId()->equals($id)) {
                return true;
            }
        }
        return false;
    }

    public function findById(HistoricalDayId $id): HistoricalDay
    {
        foreach ($this->days as $historicalDay) {
            if ($historicalDay->getId()->equals($id)) {
                return $historicalDay;
            }
        }

        throw new HistoricalDayNotFoundException();
    }

    /**
     * @throws HistoricalDayNotFoundException
     */
    public function findByHistoricalHourId(HistoricalHourId $hourId): HistoricalDay
    {
        $dayId = new HistoricalDayId($hourId->getLocationTime()->getPoint(), $hourId->getHistoricalDate());

        return $this->findById($dayId);
    }

    public function existdByHistoricalHourId(HistoricalHourId $hourId): bool
    {
        $dayId = new HistoricalDayId($hourId->getLocationTime()->getPoint(), $hourId->getHistoricalDate());

        return $this->existById($dayId);
    }

    public function clean(): void
    {
        $listElementsToRemove = [];
        for ($i = 0; $i < count($this->days); $i++) {
            $day = $this->days[$i];
            try {
                $content = json_decode($day->getBrutContent());
                if (is_object($content) && (isset($content->success) || (!isset($content->historical)))) {
                    $listElementsToRemove[$i] = $i;
                }
            } catch (JsonException $e) {
                $listElementsToRemove[$i] = $i;
            }
        }

        $this->days = array_diff_key($this->days, $listElementsToRemove);
    }
}

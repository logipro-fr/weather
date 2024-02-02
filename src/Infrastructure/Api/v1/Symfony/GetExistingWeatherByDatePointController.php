<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\InputBag;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPoint;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPointRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\ServiceController;
use Weather\Infrastructure\Shared\Tools\ArgumentParser;

class GetExistingWeatherByDatePointController extends RequestController
{
    private const INVALID_ARGUMENT_CODE = 400;
    private const POINT_ARGUMENT = "point";
    private const DATE_ARGUMENT = "date";

    public function __construct(protected WeatherInfoRepositoryInterface $repository)
    {
    }

    protected function createService(): FetchDataByDateAndPoint
    {
        $presenter = new PresenterJson();
        return new FetchDataByDateAndPoint($presenter, $this->repository);
    }

    protected function createRequest(InputBag $query): FetchDataByDateAndPointRequest
    {
        $parser = new ArgumentParser();
        if (null === $query->get(self::POINT_ARGUMENT, null)) {
            throw new InvalidArgumentException("no \"point\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $pointString */
        $pointString = $query->get(self::POINT_ARGUMENT);
        $point = $parser->stringToPoint($pointString);

        if (null === $query->get(self::DATE_ARGUMENT, null)) {
            throw new InvalidArgumentException("no \"date\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $dateString */
        $dateString = $query->get(self::DATE_ARGUMENT);
        $date = $parser->extractDate($dateString);

        /** @var bool|null $historicalOnly */
        $historicalOnly = $query->get("historicalOnly", null);

        /** @var bool $exact */
        $exact = $query->get("exact", false);

        return new FetchDataByDateAndPointRequest($point, $date, $historicalOnly, $exact);
    }
}

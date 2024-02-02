<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\InputBag;
use Weather\Infrastructure\External\WeatherApiInterface;
use Weather\Application\GetWeather\GetWeather;
use Weather\Application\GetWeather\GetWeatherRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\ServiceController;
use Weather\Infrastructure\Shared\Tools\ArgumentParser;

// fetch_data_from_API: /api/v1/fetch
class GetNewWeatherController extends RequestController
{
    private const INVALID_ARGUMENT_CODE = 400;
    private const POINT_ARGUMENT = "points";
    private const DATE_ARGUMENT = "date";

    public function __construct(
        protected WeatherInfoRepositoryInterface $repository,
        protected WeatherApiInterface $api
    ) {
    }

    protected function createService(): GetWeather
    {
        $presenter = new PresenterJson();
        return new GetWeather($presenter, $this->api, $this->repository);
    }

    protected function createRequest(InputBag $query): GetWeatherRequest
    {
        $parser = new ArgumentParser();
        if (null === $query->get(self::POINT_ARGUMENT)) {
            throw new InvalidArgumentException("no points given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $pointString */
        $pointString = $query->get(self::POINT_ARGUMENT);

        $points = $parser->extractPoints($pointString);


        if (null === $query->get(self::DATE_ARGUMENT)) {
            throw new InvalidArgumentException("no \"date\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $dateString */
        $dateString = $query->get(self::DATE_ARGUMENT);
        $date = $parser->extractDate($dateString);
        return new GetWeatherRequest($points, $date);
    }
}

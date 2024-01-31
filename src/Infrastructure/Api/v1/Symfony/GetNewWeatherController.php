<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use DateTimeZone;
use Safe\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Weather\Infrastructure\External\WeatherApiInterface;
use Weather\Application\GetWeather\GetWeather;
use Weather\Application\GetWeather\GetWeatherRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Controller;
use Weather\Tests\Features\FakeWeatherApi;

class GetNewWeatherController extends AbstractController
{
    private const DATE_FORMAT = "Y-m-d H:i:s";

    public function __construct(
        protected WeatherInfoRepositoryInterface $repository,
        protected WeatherApiInterface $api
    ) {
    }

    #[Route('/api/v1/fetch', name: "get new weather", methods: ['GET'])]
    public function getWeatherFromApi(Request $request): Response
    {
        $controller = $this->createController();
        $controller->execute($this->createRequest($request->query));
        return new Response($controller->readResponse(), $controller->readStatus(), $controller->readHeaders());
    }

    private function createController(): Controller
    {
        $presenter = new PresenterJson();
        return new Controller(new GetWeather($presenter, $this->api, $this->repository));
    }

    private function createRequest(InputBag $query): GetWeatherRequest
    {
        /** @var string $pointString */
        $pointString = $query->get("points");
        $points = $this->extractPoints($pointString);
        /** @var string $dateString */
        $dateString = $query->get("date");
        $date = DateTimeImmutable::createFromFormat(
            self::DATE_FORMAT,
            $dateString,
            new DateTimeZone(date_default_timezone_get())
        );
        return new GetWeatherRequest($points, $date);
    }

    /**
     * @return array<Point>
     */
    private function extractPoints(string $pointsString): array
    {
        $res = [];
        foreach (explode(";", $pointsString) as $point) {
            array_push($res, $this->stringToPoint($point));
        }
        return $res;
    }

    private function stringToPoint(string $value): Point
    {
        $value = explode(",", $value);
        return new Point(floatval($value[0]), floatval($value[1]));
    }
}

<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPoint;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPointRequest;
use Weather\Application\FetchData\ById\FetchDataById;
use Weather\Application\FetchData\ById\FetchDataByIdRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Controller;
use Weather\Infrastructure\Shared\Tools\ArgumentParser;

class GetExistingWeatherController extends AbstractController
{
    private const INVALID_ARGUMENT_CODE = 400;
    private const IDENTIFIER_ARGUMENT = "id";
    private const POINT_ARGUMENT = "point";
    private const DATE_ARGUMENT = "date";

    public function __construct(protected WeatherInfoRepositoryInterface $repository)
    {
    }

    #[Route('/api/v1/data/by-id', name: "get stored weather data from ID", methods: ['GET'])]
    public function getWeatherFromApiById(Request $request): Response
    {
        $controller = $this->createIDController();
        try {
            $controller->execute($this->createIdRequest($request->query));
        } catch (InvalidArgumentException $e) {
            $controller->writeUnsuccessfulResponse($e);
        }
        return new Response($controller->readResponse(), $controller->readStatus(), $controller->readHeaders());
    }

    #[Route('/api/v1/data/by-date-point', name: "get stored weather data from date and point", methods: ['GET'])]
    public function getWeatherFromApiByDateAndPoint(Request $request): Response
    {
        $controller = $this->createDateAndPointController();
        try {
            $controller->execute($this->createDateAndPointRequest($request->query));
        } catch (InvalidArgumentException $e) {
            $controller->writeUnsuccessfulResponse($e);
        }
        return new Response($controller->readResponse(), $controller->readStatus(), $controller->readHeaders());
    }

    private function createIDController(): Controller
    {
        $presenter = new PresenterJson();
        return new Controller(new FetchDataById($presenter, $this->repository));
    }

    private function createIdRequest(InputBag $query): FetchDataByIdRequest
    {
        if (null === $query->get(self::IDENTIFIER_ARGUMENT)) {
            throw new InvalidArgumentException("no identifier given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string id */
        $id = $query->get(self::IDENTIFIER_ARGUMENT);
        return new FetchDataByIdRequest($id);
    }

    private function createDateAndPointController(): Controller
    {
        $presenter = new PresenterJson();
        return new Controller(new FetchDataByDateAndPoint($presenter, $this->repository));
    }

    private function createDateAndPointRequest(InputBag $query): FetchDataByDateAndPointRequest
    {
        $parser = new ArgumentParser();
        if (null === $query->get(self::POINT_ARGUMENT, null)) {
            throw new InvalidArgumentException("no points given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $pointString */
        $pointString = $query->get(self::POINT_ARGUMENT);
        $point = $parser->stringToPoint($pointString);

        if (null === $query->get(self::DATE_ARGUMENT, null)) {
            throw new InvalidArgumentException("no date given", self::INVALID_ARGUMENT_CODE);
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

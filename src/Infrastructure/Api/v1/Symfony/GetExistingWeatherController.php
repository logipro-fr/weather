<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Safe\DateTimeImmutable;
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
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Controller;

use function SafePHP\floatval;

class GetExistingWeatherController extends AbstractController
{
    public function __construct(protected WeatherInfoRepositoryInterface $repository)
    {
    }

    #[Route('/api/v1/data/by-id', name: "get stared weather data from ID", methods: ['GET'])]
    public function getWeatherFromApiById(Request $request): Response
    {
        $controller = $this->createIDController();
        $controller->execute($this->createIdRequest($request->query));
        return new Response($controller->readResponse(), $controller->readStatus(), $controller->readHeaders());
    }

    #[Route('/api/v1/data/by-date-point', name: "get stared weather data from ID", methods: ['GET'])]
    public function getWeatherFromApiByDateAndPoint(Request $request): Response
    {
        $controller = $this->createDateAndPointController();
        $controller->execute($this->createDateAndPointRequest($request->query));
        return new Response($controller->readResponse(), $controller->readStatus(), $controller->readHeaders());
    }

    private function createIDController(): Controller
    {
        $presenter = new PresenterJson();
        return new Controller(new FetchDataById($presenter, $this->repository));
    }

    private function createIdRequest(InputBag $query): FetchDataByIdRequest
    {
        /** @var string id */
        $id = $query->get("id");
        return new FetchDataByIdRequest($id);
    }

    private function createDateAndPointController(): Controller
    {
        $presenter = new PresenterJson();
        return new Controller(new FetchDataByDateAndPoint($presenter, $this->repository));
    }

    private function createDateAndPointRequest(InputBag $query): FetchDataByDateAndPointRequest
    {
        /** @var string $pointString */
        $pointString = $query->get("point");
        $pointArray = explode(",", $pointString);
        $point = new Point(floatval($pointArray[0]), floatval($pointArray[1]));
        /** @var string $dateString */
        $dateString = $query->get("date");
        if ($query->get("historicalOnly") !== null) {
            /** @var ?bool $historicalOnly */
            $historicalOnly = $query->get("historicalOnly");
        } else {
            /** @var ?bool $historicalOnly */
            $historicalOnly = null;
        }
        if ($query->get("exact") !== null) {
            /** @var bool $exact */
            $exact = $query->get("exact");
        } else {
        /** @var bool $exact */
            $exact = false;
        }
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", $dateString);
        return new FetchDataByDateAndPointRequest($point, $date, $historicalOnly, $exact);
    }
}

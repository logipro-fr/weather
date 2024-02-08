<?php

namespace Weather\Tests\Features;

use Behat\Behat\Context\Context;
use DateTimeZone;
use PHPUnit\Framework\Assert;
use Safe\DateTimeImmutable;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPoint;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPointRequest;
use Weather\Application\FetchData\ById\FetchDataById;
use Weather\Application\FetchData\ById\FetchDataByIdRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

use function SafePHP\floatval;

/**
 * Defines application features from the specific context.
 */
class FetchDataContext implements Context
{
    /** @var array<mixed> $in */
    private array $in;
    private WeatherInfoRepositoryInMemory $repository;

    public function __construct()
    {
        $this->repository = new WeatherInfoRepositoryInMemory();
    }
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    /**
     * @Given there is an ID :arg1 reffering to an ID
     */
    public function thereIsAnIdRefferingToAnId(string $arg1): void
    {
        $this->in["ID"] = $arg1;
        $this->repository = new WeatherInfoRepositoryInMemory();
        $this->repository->save(
            new WeatherInfo(
                new Point(0, 0),
                DateTimeImmutable::createFromFormat("Ymd", "20240101"),
                "{}",
                Source::DEBUG,
                false,
                new WeatherInfoId($this->in["ID"])
            )
        );
    }

    /**
     * @When the user request to fetch the corresponding point
     */
    public function theUserRequestToFetchTheCorrespondingPoint(): void
    {
        /** @var string $id */
        $id = $this->in["ID"];
        $this->in["request"] = new FetchDataByIdRequest($id);
    }

    /**
     * @Then the user gets a response containing point corresponding to the ID
     */
    public function theUserGetsAResponseContainingPointCorrespondingToTheId(): void
    {
        $presenter = new PresenterObject();
        $service = new FetchDataById($presenter, $this->repository);
        /** @var FetchDataByIdRequest $request */
        $request = $this->in["request"];
        $service->execute($request);
        /** @var string $id */
        $id = $this->in["ID"];
        /** @var WeatherInfo $data */
        $data = $presenter->read()->getData();
        Assert::assertEquals(new WeatherInfoId($id), $data->getId());
    }

    /**
     * @Given there is a Point :arg1 and a date :arg2
     */
    public function thereIsAPointAndADate(string $arg1, string $arg2): void
    {
        $coords = explode(",", $arg1);
        $this->in["point"] = new Point(floatval($coords[0]), floatval($coords[1]));
        $this->in["date"] = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s",
            $arg2,
            new DateTimeZone(date_default_timezone_get())
        );
        $this->in["source"] = Source::DEBUG;

        $this->repository->save(new WeatherInfo($this->in["point"], $this->in["date"], "{}", $this->in["source"]));
    }

    /**
     * @When the user request to fetch the point
     */
    public function theUserRequestToFetchThePoint(): void
    {
        /** @var Point $point */
        $point = $this->in["point"];
        /** @var DateTimeImmutable $date */
        $date = $this->in["date"];
        $this->in["request"] = new FetchDataByDateAndPointRequest($point, $date, null, true);
    }

    /**
     * @Then the user gets a response containing a corresponding point
     */
    public function theUserGetsAResponseContainingACorrespondingPoint(): void
    {
        $presenter = new PresenterObject();
        $service = new FetchDataByDateAndPoint($presenter, $this->repository);
        /** @var FetchDataByDateAndPointRequest $request */
        $request = $this->in["request"];
        $service->execute($request);
        /** @var WeatherInfo $info */
        $info = $presenter->read()->getData();
        Assert::assertEquals($this->in["point"], $info->getPoint());
        Assert::assertEquals($this->in["date"], $info->getDate());
    }

    /**
     * @When the user request to fetch a nearby point
     */
    public function theUserRequestToFetchANearbyPoint(): void
    {
        /** @var Point $actualPoint */
        $actualPoint = $this->in["point"];
        /** @var DateTimeImmutable $date */
        $date = $this->in["date"];
        $point = new Point($actualPoint->getLatitude() + 0.004, $actualPoint->getLongitude() - 0.004);
        $this->in["request"] = new FetchDataByDateAndPointRequest($point, $date);
    }
}

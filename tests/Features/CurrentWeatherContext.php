<?php

namespace Weather\Tests\Features;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Safe\DateTimeImmutable;
use Weather\Application\GetWeather\GetWeather;
use Weather\Application\GetWeather\GetWeatherRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

use function SafePHP\floatval;

/**
 * Defines application features from the specific context.
 */
class CurrentWeatherContext implements Context
{
    private PresenterObject $presenter;
    private FakeWeatherApi $api;
    private WeatherInfoRepositoryInterface $repository;
    private GetWeather $service;
    private DateTimeImmutable $currentDate;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->api = new FakeWeatherApi();
        $this->presenter = new PresenterObject();
        $this->repository = new WeatherInfoRepositoryInMemory();
        $this->currentDate = new DateTimeImmutable("2024-01-17 16:26");
    }

    /**
     * @var array<Point>
     */
    private array $points;

    /**
     * @Given there is a list of points:
     */
    public function thereIsAListOfPoints(TableNode $table): void
    {
        $this->points = [];
        /** @var array<string,float> $row */
        foreach ($table as $row) {
            array_push($this->points, new Point(floatval($row['latitude']), floatval($row['longitude'])));
        }
    }

    private GetWeatherRequest $request;
    //not actually current, but we'll pretend it it for the sake of deterministic testing

    /**
     * @When there is a request for the current weather at these points
     */
    public function thereIsARequestForTheCurrentWeatherAtThesePoints(): void
    {
        $this->request = new GetWeatherRequest($this->points, $this->currentDate);
    }

    /**
     * @Then user gets a response containing weather at these points
     */
    public function userGetsAResponseContainingWeatherAtThesePoints(): void
    {
        $this->service = new GetWeather($this->presenter, $this->api, $this->repository);

        $this->service->execute($this->request);

        $response = $this->presenter->read();

        /** @var array<WeatherInfo> $infos */
        $infos = $response->getData();
        Assert::assertIsString($infos[0]->getData());
    }
}

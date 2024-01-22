<?php

namespace Weather\Tests\Features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Weather\Application\ImportLegacy\ImportLegacy;
use Weather\Application\ImportLegacy\ImportLegacyRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

/**
 * Defines application features from the specific context.
 */
class LegacyImportContext implements Context
{
    private string $source;
    private ImportLegacy $service;
    private WeatherInfoRepositoryInterface $repository;
    private PresenterObject $presenter;
    private ImportLegacyRequest $request;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->presenter = new PresenterObject();
        $this->repository = new WeatherInfoRepositoryInMemory();
        $this->service = new ImportLegacy($this->presenter, $this->repository);
    }

    /**
     * @Given there is a path :arg1 containing the data
     */
    public function thereIsAPathContainingTheData(string $arg1)
    {
        $this->source = $arg1;
    }

    /**
     * @When the user request to have it imported
     */
    public function theUserRequestToHaveItImported()
    {
        $this->request = new ImportLegacyRequest($this->source);
    }

    /**
     * @Then the data should be imported in the new repository
     */
    public function theDataShouldBeImportedInTheNewRepository()
    {
        $this->service->execute($this->request);
    }

    /**
     * @Given there is data contained in a :database
     */
    public function thereIsDataContainedInADatabase()
    {
        throw new PendingException();
    }

    /**
     * @When the user request to have it extracted
     */
    public function theUserRequestToHaveItExtracted()
    {
        throw new PendingException();
    }
}

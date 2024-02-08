<?php

namespace Weather\Tests\Features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Weather\Application\ImportLegacy\ImportLegacyFile;
use Weather\Application\ImportLegacy\ImportLegacyFileRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

/**
 * Defines application features from the specific context.
 */
class LegacyImportContext implements Context
{
    private string $source;
    private ImportLegacyFile $service;
    private WeatherInfoRepositoryInterface $repository;
    private PresenterObject $presenter;
    private ImportLegacyFileRequest $request;
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
        $this->service = new ImportLegacyFile($this->presenter, $this->repository);
    }

    /**
     * @Given there is a path :arg1 containing the data
     */
    public function thereIsAPathContainingTheData(string $arg1): void
    {
        $this->source = $arg1;
    }

    /**
     * @When the user request to have it imported
     */
    public function theUserRequestToHaveItImported(): void
    {
        $this->request = new ImportLegacyFileRequest($this->source);
    }

    /**
     * @Then the data should be imported in the new repository
     */
    public function theDataShouldBeImportedInTheNewRepository(): void
    {
        $this->service->execute($this->request);
    }

    /**
     * @Given there is data contained in a :database
     */
    public function thereIsDataContainedInADatabase(): void
    {
        throw new PendingException();
    }

    /**
     * @When the user request to have it extracted
     */
    public function theUserRequestToHaveItExtracted(): void
    {
        throw new PendingException();
    }
}

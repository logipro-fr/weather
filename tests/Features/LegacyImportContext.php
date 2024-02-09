<?php

namespace Weather\Tests\Features;

use Behat\Behat\Context\Context;
use Weather\Application\ImportLegacy\ImportLegacyFile;
use Weather\Application\ImportLegacy\ImportLegacyFileRequest;
use Weather\Application\ImportLegacy\ImportLegacySQL;
use Weather\Application\ImportLegacy\ImportLegacySQLRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Application\Presenter\RequestInterface;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

/**
 * Defines application features from the specific context.
 */
class LegacyImportContext implements Context
{
    private string $source;
    private string $db;
    private string $table;
    private ImportLegacyFile $serviceFile;
    private ImportLegacySQL $serviceSql;
    private WeatherInfoRepositoryInterface $repository;
    private PresenterObject $presenter;
    private RequestInterface $request;
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
        $this->serviceFile = new ImportLegacyFile($this->presenter, $this->repository);
        $this->serviceSql = new ImportLegacySQL($this->presenter, $this->repository);
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
        $this->serviceFile->execute($this->request);
    }

    /**
     * @Given there is data contained in a database :database and table :table
     */
    public function thereIsDataContainedInADatabase(string $arg1, string $arg2): void
    {
        $this->db = $arg1;
        $this->table = $arg2;
    }

    /**
     * @When the user request to have it extracted
     */
    public function theUserRequestToHaveItExtracted(): void
    {
        $this->request = new ImportLegacySQLRequest($this->db, $this->table, "weather", "weather");
    }

    /**
     * @Then the database should be imported in the new repository
     */
    public function theDatabaseShouldBeImportedInTheNewRepository(): void
    {
        $this->serviceSql->execute($this->request);
    }
}

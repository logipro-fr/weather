<?php

namespace Features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class CurrentWeatherContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given there is a list of points:
     */
    public function thereIsAListOfPoints(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @When there is a request for the current weather at these points
     */
    public function thereIsARequestForTheCurrentWeatherAtThesePoints()
    {
        throw new PendingException();
    }

    /**
     * @Then user gets a response containing weather at these points
     */
    public function userGetsAResponseContainingWeatherAtThesePoints()
    {
        throw new PendingException();
    }
}

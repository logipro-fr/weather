Feature: Get current weather at some points

    Scenario: Get weather at some points
        Given there is a list of points:
            | latitude  | longitude |
            | 52.516667 | 13.383333 |
            | 48.137222 | 11.575556 |
            | 51.050000 | 13.733333 |
        When there is a request for the current weather at these points
        Then user gets a response containing weather at these points
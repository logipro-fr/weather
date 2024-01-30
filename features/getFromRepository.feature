Feature: Fetch data from current repository

    Scenario: Fetch by id
        Given there is an ID "weather_9768edf325a1354c351a135b135e31cf" reffering to an ID 
        When the user request to fetch the corresponding point
        Then the user gets a response containing point corresponding to the ID

    Scenario: Fetch by date and point
        Given there is a Point "41.867,2.333" and a date "2024-01-01 12:30:00" 
        When the user request to fetch the point
        Then the user gets a response containing a corresponding point

    Scenario: Fetch imprecisely by date and point
        Given there is a Point "41.867,2.333" and a date "2024-01-01 12:30:00" 
        When the user request to fetch a nearby point
        Then the user gets a response containing a corresponding point
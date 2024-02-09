Feature: Import data from legacy repository

    Scenario: Import from dir
        Given there is a path "tests/Features/data/2024/01/02/2024-01-02-11-08.json" containing the data 
        When the user request to have it imported
        Then the data should be imported in the new repository 

    Scenario: Import from dir
        Given there is a path "tests/Features/data/2024/" containing the data 
        When the user request to have it imported
        Then the data should be imported in the new repository 



    Scenario: Import from database
        Given there is data contained in a database "mysql:host=weather-mariadb:3306;dbname=weather" and table "currentweathers"
        When the user request to have it extracted
        Then the database should be imported in the new repository 
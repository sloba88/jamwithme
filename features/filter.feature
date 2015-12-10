Feature: Filter
  In order to search other users
  As a user
  I want to be able to use filters

  Background:
    Given I am logged in as user

  Scenario: Filtering results
    When I am on the homepage
    Then I should see "Slobodan"
    Then I select2 tag ".filter-instruments" with "Drums"
    Then I wait for 1 seconds
    Then I follow "anna"
    Then I should see "Anna Hamalainen"
    Then I follow "Drums"
    Then I should see "mstanic"

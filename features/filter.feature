Feature: Jamifind
  In order to login
  As a user
  I want to be able to register

  Background:
    Given I am logged in as administrator

  Scenario: Viewing a register page
    When I am on the homepage
    And I should see "login to jamifind"
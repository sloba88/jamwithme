Feature: Shout
  In order to shout
  As a user
  I want to be able to use shout box

  Background:
    Given I am logged in as user

  Scenario: Filtering results
    When I am on the homepage
    Then I fill in "shout_text" with "Testing Shout"
    And I press "shoutSend"
    Then I should see "just now"
    Then I should see "Testing Shout"

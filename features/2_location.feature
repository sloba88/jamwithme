Feature: Filter
  In order to search other users
  As a user
  I want to be able to use filters

  Background:
    Given I am logged in as user

  Scenario: Setting a location
    When I am on the homepage
    And I follow "settings"
    And I fill in "fos_user_profile_form_location_address" with "Lauttasaari"
    And I follow "Lauttasaari, Helsinki, Finland"
    And I press "Save"
    Then I should see "The profile has been updated"
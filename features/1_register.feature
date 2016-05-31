Feature: Register
  In order to login
  As a user
  I want to be able to register

  Scenario: Viewing a register page
    When I am on the homepage
    Then I should see "SIGN UP TO JAMIFIND"
    And I fill in "username" with "test2"
    And I fill in "email" with "test2@test2.com"
    And I fill in "password" with "testtest"
    And I fill in "repeat password" with "testtest"
    And I click on the element with css selector "label.fos_user_registration_form_acceptedTerms_label"
    And I press "acceptTerms"
    And I press "sign up"
    Then I should see "setup your profile"
    Then I select2 ".instrument-select" with "Acoustic Guitar"
    And I follow "location-tab"
    And I fill in "fos_user_profile_form_location_address" with "Lauttasaari"
    And I wait for 2 seconds
    And I follow "Lauttasaari, Helsinki, Finland"
    And I press "finish-1"
    Then I should see "The profile has been updated"
Feature: Register
  In order to login
  As a user
  I want to be able to register

  Scenario: Viewing a register page
    When I am on the homepage
    And I should see "login to jamifind"
    Then I follow "sign up"
    Then I should see "SIGN UP TO JAMIFIND"
    And I fill in "Username" with "test2"
    And I fill in "email" with "test2@test2.com"
    And I fill in "password" with "test2"
    And I fill in "repeat password" with "test2"
    And I press "sign up"
    Then I should see "find compatible musicians"

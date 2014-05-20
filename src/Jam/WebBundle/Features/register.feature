Feature: Musician feature
    In order to create and join jams
    As a musician
    I want to be able to register

    Scenario: Register as a new user
      Given I go to register page
      When I fill in email with "test@test.com"
      And I fill in username with "test"
      And I fill in password with "test"
      And I fill in verification with "test"
      And I fill in first_name with "test"
      And I fill in last_name with "test"
      And I fill in about with "test"
      Then I should see " "
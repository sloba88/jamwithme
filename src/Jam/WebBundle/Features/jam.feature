Feature: Jam feature
    In order to interact with other musicians
    As a musician
    I want to be able to manage jams

    Scenario: Create a new Jam
      Given I go to start_jam page
      When I fill in name with "test"
      And I fill in description with "test"
      And I fill in members_count with "2"
      And I press "save"
      Then I should see "Jam created successfully."

    Scenario: Edit a Jam
      Given I go to "jam/edit/test" page
      When I fill in name with "test2"
      And I fill in description with "test2"
      And I fill in members_count with "3"
      And I press "save"
      Then I should see "Jam updated successfully."


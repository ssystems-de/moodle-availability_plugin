@availability @availability_plugin @javascript
Feature: Restrict section visibility by installed plugin
  In order to control section visibility based on installed plugins
  As an admin
  I need to configure the availability_plugin condition and have it enforced for students

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
      | manager1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | manager1 | C1     | manager        |

  Scenario Outline: Section is visible / not visible when required plugin exists / does not exist
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I edit the section "1"
    And I set the field "Section name" to "My test section"
    And I expand all fieldsets
    And I click on "Add restriction" "button"
    And I click on "Installed plugin" "button" in the "Add restriction..." "dialogue"
    And I set the field "Technical plugin name (e.g. mod_assign)" to "<component>"
    And I press "Save changes"
    Then I should see "The plugin <component> is installed"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "My test section"
    And I <shouldornot> see "Not available unless: The plugin <component> is installed"

    Examples:
      | component  | shouldornot |
      | mod_assign | should not  |
      | mod_foobar | should      |

  Scenario: Reject empty plugin name input at save time
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I edit the section "1"
    And I expand all fieldsets
    And I click on "Add restriction" "button"
    And I click on "Installed plugin" "button" in the "Add restriction..." "dialogue"
    Then I should see "No plugin name specified" in the ".availability-item" "css_element"
    And I press "Save changes"
    And I should see "No plugin name specified" in the ".availability-item" "css_element"
    And I should see "No plugin name specified" in the "#id_error_availabilityconditionsjson" "css_element"

  Scenario Outline: Only users with the proper capability can add the condition
    Given the following "permission overrides" exist:
      | capability                      | permission | role    | contextlevel | reference |
      | availability/plugin:addinstance | Allow      | manager | System       |           |
    And I log in as "<user>"
    And I am on "Course 1" course homepage with editing mode on
    When I edit the section "1"
    And I expand all fieldsets
    And I click on "Add restriction" "button"
    Then I <shouldornot> see "Installed plugin" in the "Add restriction..." "dialogue"

    Examples:
      | user     | shouldornot |
      | teacher1 | should not  |
      | manager1 | should      |

  # Unfortunately, this can't be tested with Behat does not have access to the visible datalist proposals
  # Scenario: Autocomplete suggestions are shown when typing plugin name

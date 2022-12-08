@local @local_coodle @javascript

Feature: Test purchase process in shopping cart.
  In order to buy an item
  As a student
  I need to put an item in my cart and proceed to checkout

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Username  | 1        |
      | user2    | Username  | 2        |
      | teacher  | Teacher   | 3        |
      | manager  | Manager   | 4        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | user1    | C1     | student        |
      | user2    | C1     | student        |
      | teacher  | C1     | editingteacher |

  @javascript
  Scenario: Add and advisor and client check QR CODE and Course
    And I add an "teacher" advisor "user1" user
    Given I log in as "user1"
    And I visit "/local/coodle/myusers.php"
    And I click on ".fa-qrcode" "css_element"
    And I click on ".show" "css_element"
    And I click on "[data-view=\"course\"]" "css_element"
    And I add an "manager" advisor "user2" user
    And I visit "/local/coodle/myusers.php"

  Scenario: Add a Todo
    Given I log in as "user1"
    And I add an "teacher" advisor "user1" user
    And I visit "/local/coodle/myusers.php"
    And I click on "[data-action='local-coodle-add-todo']" "css_element"

  Scenario: I as an advisor login an add a user to my list
    Given I log in as "user1"
    And I add an "user1" advisor "user2" user
    And I visit "/local/coodle/myusers.php"
    And I click on "[data-action='coodle-user_create_form']" "css_element"
    And I set the following fields to these values:
      | Username      | s2                |
      | First name    | Jane              |
      | Surname       | Doe               |
      | Email address | test@example.com  |
      | New password  | #Asdfgh8          |
    And I press "Save changes"
    And I visit "/local/coodle/myusers.php"
    And I wait "10" seconds

    






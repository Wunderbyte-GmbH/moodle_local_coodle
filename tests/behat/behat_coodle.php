<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Behat question-related steps definitions.
 *
 * @package    local_shopping_cart
 * @category   test
 * @copyright  2022 Wunderbyte Gmbh <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_shopping_cart\local\entities\cartitem;
use local_shopping_cart\shopping_cart;

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

/**
 * Steps definitions related with the mooduell table management.
 *
 * @package    local_coodle
 * @category   test
 * @copyright  2022 Wunderbyte Gmbh <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_coodle extends behat_base {

    /**
     * Add advisor and user.
     * This ads a dummy item to the cache. After reloading the page, the item will be visible.
     * "(?P<itemname_string>(?:[^"]|\\")*)"
     * @Given /^I add an "(?P<advisorname>(?:[^"]|\\")*)" advisor "(?P<username>(?:[^"]|\\")*)" user$/
     * @return void
     */
    public function i_add_an_advisor(string $advisorname, string $username) {
        $advisor = \core_user::get_user_by_username($advisorname);
        $courseid = \local_coodle\advisor::create_course_for_adivisor($advisor->id);
        $createadvisor = new \local_coodle\advisor($advisor->id, $courseid,  true);
        $user = \core_user::get_user_by_username($username);
        \local_coodle\coodle_user::create_coodle_user($user->id, $advisor->id);
    }
}

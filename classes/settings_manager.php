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

namespace local_coodle;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class settings_manager.
 *
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_manager {

    /**
     * Userid of advisor
     *
     * @var int
     */
    public $coursecategoryid;

    /**
     * Returns the categoryid in which advisorcourses are listed and created
     *
     * @return int
     */
    public static function create_or_get_standard_coursecategory() : int {
        $categoryid = get_config('local_coodle', 'coursecategory');
        if (empty(get_config('local_coodle', 'coursecategory'))) {
            $coursecategory = \core_course_category::create(array('name' => 'BeraterInnen'));
            set_config('coursecategory', $coursecategory->id, 'local_coodle');
            $categoryid = $coursecategory->id;
        }
        return $categoryid;
    }

    /**
     * Check if user is a Coodle advisor
     *
     * @param int $userid
     * @return boolean
     */
    public static function is_advisor($userid = 0) : bool {
        global $DB, $USER;
        if (empty($userid)) {
            $userid = $USER->id;
        }
        return $DB->record_exists('local_coodle_advisor', array('userid' => $userid));
    }

    /**
     * Deletes an advisor fomr coodle table
     */
    private function delete_advisor($userid) {
        global $DB;
        return $DB->delete_records('local_coodle_advisor', array('userid' => $userid));
    }

    /**
     * Deletes an advisor fomr coodle table
     */
    private function delete_coodle_user($userid) {
        global $DB;
        return $DB->delete_records('local_coodle_user', array('userid' => $userid));
    }

}

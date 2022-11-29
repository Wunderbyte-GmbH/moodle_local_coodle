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

use stdClass;

/**
 * Class advisor.
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coodle_user {

    public function __construct() {
    }

    /**
     * Creates a course with the name of the advisor
     *
     * @param int $userid
     * @param int $advisorid
     * @return int
     */
    public static function create_coodle_user(int $userid, int $advisorid = null) {
        global $DB;
        $data = new stdClass();
        $data->userid = $userid;
        $data->advisorid = $advisorid;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->deleted = 0;

        $coodleuserid = $DB->insert_record('local_coodle_user', $data, true);
        if (!empty($advisorid)) {
            //\local_coodle\advisor::course_manual_enrolments(array(3), array($guestuserid), 5);
        }
        return $coodleuserid;
    }

    public static function enrol_coodle_user($advisorid) {
        $a = 2;
    }

    public static function get_coodle_users() {
        global $DB;
        $sql = "SELECT cu.*, u.firstname as 'clientfirstname', u.lastname as 'clientlastname',
        ua.firstname as 'advisorfirstname', ua.lastname as 'advisorlastname', a.courseid
         FROM {local_coodle_advisor} a RIGHT JOIN {local_coodle_user} cu on a.userid = cu.advisorid
         JOIN {user} u on cu.userid = u.id
         LEFT JOIN {user} ua on cu.advisorid = ua.id";
        $data = $DB->get_records_sql($sql);
        return $data;
    }

    public static function prepare_for_template() {
        $coodleusers = self::get_coodle_users();
        $templatedata = [];
        foreach ($coodleusers as $coodleuser) {
            $tdata = $coodleuser;
            $tdata->userdatecreated = date("Y-m-d", $coodleuser->timecreated);
            $templatedata[] = $tdata;
        }
        return $templatedata;
    }
}

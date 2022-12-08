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
        global $DB, $USER;
        $data = new stdClass();
        $data->userid = $userid;
        if (empty($advisorid)) {
            $advisorid = $USER->id;
        }
        $data->advisorid = $advisorid;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->deleted = 0;

        $coodleuserid = $DB->insert_record('local_coodle_user', $data, true);

        return $coodleuserid;
    }

    /**
     * Gets all coodle users (clients) with user data from MOODLE user table
     *
     * @return array
     */
    public static function get_coodle_users() {
        global $DB;
        $sql = "SELECT cu.*, u.firstname AS clientfirstname, u.lastname as clientlastname,
        ua.firstname as advisorfirstname, ua.lastname as advisorlastname, a.courseid
         FROM {local_coodle_advisor} a RIGHT JOIN {local_coodle_user} cu on a.userid = cu.advisorid
         JOIN {user} u on cu.userid = u.id
         LEFT JOIN {user} ua on cu.advisorid = ua.id";
        $data = $DB->get_records_sql($sql);
        return $data;
    }

    /**
     * Prepares date for mustache template
     *
     * @return array
     */
    public static function prepare_for_template() {
        $coodleusers = self::get_coodle_users();
        $templatedata = [];
        foreach ($coodleusers as $coodleuser) {
            $tdata = $coodleuser;
            $tdata->userdatecreated = date("Y-m-d", $coodleuser->timecreated);
            $qrcodeforappstr = get_string('qrcodeformobileappaccess', 'tool_mobile');

            $mobilesettings = get_config('tool_mobile');
            $mobilesettings->qrcodetype = \local_coodle\overrides\mobileapioverrides::QR_CODE_LOGIN;
            $qrcodeimg = \local_coodle\overrides\mobileapioverrides::generate_login_qrcode_from_userid($mobilesettings, $coodleuser->userid);
            $mobileqr = \html_writer::link('#qrcode-'.$coodleuser->userid, '',
                ['class' => 'btn btn-primary mt-2 fa fa-qrcode', 'data-toggle' => 'collapse',
                'role' => 'button', 'aria-expanded' => 'false']);
            $mobileqr .= \html_writer::div(\html_writer::img($qrcodeimg, $qrcodeforappstr, ['class' => 'qrcode']), 'collapse mt-4', ['id' => 'qrcode-'.$coodleuser->userid]);
            $tdata->qrcode = $mobileqr;
            $templatedata[] = $tdata;
        }
        return $templatedata;
    }


    /**
     * Counts all clients in coodle DB
     *
     * @return int
     */
    public static function count_users() {
        global $DB;
        return $DB->count_records('local_coodle_user', null);
    }
}

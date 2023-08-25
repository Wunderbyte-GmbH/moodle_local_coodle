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
 * Class coodle_links.
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2023 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coodle_links {

    public $userid;
    public $advisorid;
    public $text;
    public $timecreated;
    public $timemodified;

    protected $link;

    public function __construct($data = null) {
        global $USER;
        if(empty($data)) {
            return;
        }
        $linkdata = new stdClass();
        $linkdata->userid = $data->clientid;
        $linkdata->advisorid = $USER->id;
        $linkdata->timecreated = time();
        $linkdata->timemodified = time();
        $linkdata->deleted = 0;
        $linkdata->text = $data->text;
        $this->link = $linkdata;
    }

    public function load_linklist($userid) {
        global $DB;
        $sql = "SELECT t.*, u.firstname, u.lastname FROM {local_coodle_links} t
        JOIN {user} u ON u.id = t.userid
        WHERE t.clienitd = $userid";
        $linklist = $DB->get_records_sql($sql);
        return array_values($linklist);
    }

    public function load_linklist_by_userid(int $userid, int $status = 0) {
        global $DB;
        $sql = "SELECT t.*, u.firstname, u.lastname  FROM {local_coodle_links} t
        JOIN {user} u ON u.id = t.userid
        WHERE t.userid = $userid";
        if ($status != 0) {
            $sql .= " AND t.status = $status";
        }
        $linklist = $DB->get_records_sql($sql);
        return array_values($linklist);
    }

    /**
     * Creates a course with the name of the advisor
     *
     * @return int
     */
    public function add_link() {
        global $DB;
        $linkid = $DB->insert_record('local_coodle_links', $this->link, true);
        return $linkid;
    }

    /**
     * Gets all coodle users (clients) with user data from MOODLE user table
     *
     * @return array
     */
    public static function delete_link(int $link) {
        global $DB, $USER;
        $conditons = ['advisorid' => $USER->id, 'id' => $link];
        $DB->delete_records('local_coodle_links', $conditons);
    }

    /**
     * Prepares date for mustache template
     *
     * @return stdClass
     */
    // public static function set_link_status(int $linkid, int $status) {
    // global $DB, $USER;
    // link: ADD constants. 1 2 3 4 -.
    // $params = array(
    // 'deleted' => $status,
    // 'id' => $linkid
    // );
    // $DB->update_record('local_coodle_links', $params);
    // unset($params['deleted']);
    // return $DB->get_record('local_coodle_links', $params);
    // }
}

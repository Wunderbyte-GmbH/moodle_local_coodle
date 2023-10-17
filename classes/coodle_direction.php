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
 * Class direction.
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coodle_direction {

    public $userid;
    public $advisorid;
    public $text;
    public $timecreated;
    public $timemodified;

    public $direction;

    public function __construct($data = null, $id = null) {
        global $USER;
        if(empty($data)) {
            if(!empty($id)) {
                $this->set_direction($id);
                return;
            }
            return;
        }

        $directiondata = new stdClass();
        $directiondata->userid = $data->userid;
        $directiondata->advisorid = $USER->id;
        $directiondata->timecreated = time();
        $directiondata->timemodified = time();
        $directiondata->deleted = 0;
        $directiondata->text = $data->description;
        $directiondata->title = $data->title;
        $this->direction = $directiondata;
    }

    /**
     * Load direction from DB and set the class variable
     * @param int $id
     * @return void
     */
    public function set_direction(int $id) {
        global $DB;
        $this->direction = $DB->get_record('local_coodle_directions', ['id' => $id]);
    }

    /**
     * Load all the directions set by an specific advisor
     *
     * @param int $advisorid
     * @return void
     */
    public function load_directionlist(int $advisorid) {
        global $DB;
        $sql = "SELECT t.*, u.firstname, u.lastname FROM {local_coodle_directions} t
        JOIN {user} u ON u.id = t.userid
        WHERE t.advisorid = $advisorid";
        $directionlist = $DB->get_records_sql($sql);
        return array_values($directionlist);
    }

    /**
     * Load all the directions set for an specific user
     *
     * @param int $userid
     * @return array
     */
    public function load_directionlist_by_userid(int $userid) {
        global $DB;
        $data = $DB->get_records('local_coodle_directions', array('userid' => $userid));
        return array_values($data);
    }

    /**
     * Creates a course with the name of the advisor
     *
     * @return int
     */
    public function add_direction() {
        global $DB;
        $directionid = $DB->insert_record('local_coodle_directions', $this->direction, true);
        return $directionid;
    }


    /**
     * Creates a course with the name of the advisor
     *
     * @return boolean
     */
    public function update_direction($data) {
        global $DB;
        $directionupdated = $DB->update_record('local_coodle_directions', $data, true);
        return $directionupdated;
    }

    /**
     * Gets all coodle users (clients) with user data from MOODLE user table
     *
     * @return array
     */
    public static function delete_direction(int $directionid) {
        global $DB, $USER;
        $conditons = ['advisorid' => $USER->id, 'id' => $directionid];
        $DB->delete_records('local_coodle_directions', $conditons);
    }

    /**
     * Prepares date for mustache template
     *
     * @return stdClass
     */
    public static function set_direction_status(int $directionid, int $status) {
        global $DB, $USER;
        // direction: ADD constants. 1 2 3 4 -.
        $params = array(
            'deleted' => $status,
            'id' => $directionid
        );
        $DB->update_record('local_coodle_directions', $params);
        unset($params['deleted']);
        return $DB->get_record('local_coodle_directions', $params);
    }
}

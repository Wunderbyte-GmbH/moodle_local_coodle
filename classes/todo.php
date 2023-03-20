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
 * Class todo.
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class todo {

    public $userid;
    public $advisorid;
    public $text;
    public $timecreated;
    public $timemodified;

    protected $todo;

    public function __construct($data = null) {
        global $USER;
        if(empty($data)) {
            return;
        }
        $tododata = new stdClass();
        $tododata->userid = $data->clientid;
        $tododata->advisorid = $USER->id;
        $tododata->timecreated = time();
        $tododata->timemodified = time();
        $tododata->deleted = 0;
        $tododata->text = $data->text;
        $this->todo = $tododata;
    }

    public function load_todolist($advisorid) {
        global $DB;
        $sql = "SELECT t.*, u.firstname, u.lastname FROM {local_coodle_todos} t
        JOIN {user} u ON u.id = t.userid
        WHERE t.advisorid = $advisorid";
        $todolist = $DB->get_records_sql($sql);
        return array_values($todolist);
    }

    public function load_todolist_by_userid($userid) {
        global $DB;
        $sql = "SELECT t.*, u.firstname, u.lastname, u.id FROM {user} u
        JOIN {local_coodle_todos} t ON u.id = t.userid
        WHERE t.userid = $userid";
        $todolist = $DB->get_records_sql($sql);
        return array_values($todolist);
    }

    /**
     * Creates a course with the name of the advisor
     *
     * @return int
     */
    public function add_todo() {
        global $DB;
        $todoid = $DB->insert_record('local_coodle_todos', $this->todo, true);
        return $todoid;
    }

    /**
     * Gets all coodle users (clients) with user data from MOODLE user table
     *
     * @return array
     */
    public static function delete_todo(int $todoid) {
        global $DB, $USER;
        //Record exists
        $conditons = ['advisorid' => $USER->id, 'id' => $todoid];
        $DB->delete_records('local_coodle_todos', $conditons);
    }

    /**
     * Prepares date for mustache template
     *
     * @return array
     */
    public static function set_todo_status(int $todoid, int $status) {
        global $DB, $USER;
        $params = array(
            'deleted' => $status,
            'id' => $todoid
        );
        $DB->update_record('local_coodle_todos', $params);
    }
}

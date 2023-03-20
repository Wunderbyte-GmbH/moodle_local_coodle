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

defined('MOODLE_INTERNAL') || die();

/**
 * Class todolist. TODO: needed?
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class todolist {

    /**
     * Userid of advisor
     *
     * @var int
     */
    public $userid;

    /**
     * Courseid of Advisorcourse
     *
     * @var int
     */
    public $clientid;

    /**
     * id
     * text
     * deleted
     * userid
     * clientid
     * done
     *
     *
     * @var array
     */
    protected $todolist;

    public function __construct(int $clientid = 0, int $userid = 0) {
        global $USER;
        if ($userid = 0) {
            $userid = $USER->id;
        }
        $this->userid = $userid;
        $this->clientid = $clientid;
        $this->todolist = $this->get_todos_for_client();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function get_todos_for_client() {
        global $DB;
        $sql = "SELECT * FROM {local_coodle_todos} WHERE 'userid' = $this->userid GROUP BY 'clientid'";
        $DB->get_records_sql($sql);
    }

    /**
     * Creates a course with the name of the advisor
     *
     * @param int $userid of advisor
     * @return int
     */
    public static function create_todo($data) {
        global $DB;
        // Create todo
    }

    /**
     * Returns the personal advisor course from userid of advisor
     *
     * @param int $advisorid
     * @return int
     */
    public static function delete_todo(int $advisorid) : int {
        global $DB;
        $courseid = $DB->get_record('local_coodle_advisor', ['userid' => $advisorid], 'courseid', IGNORE_MISSING);
        // Delete todo
        return $courseid;
    }
}

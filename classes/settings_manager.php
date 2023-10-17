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

use Exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
define('COODLEADVISOR', 1);
define('COODLEUSER', 2);

/**
 * Class settings_manager.
 *
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_manager {


    private $table;

    private $token;

    private $id;

    private $userid;
    /**
     * Userid of advisor
     *
     * @var int
     */
    public $coursecategoryid;


    public function __construct(int $mode) {
        global $DB;
        if ($mode == COODLEADVISOR) {
            $this->table = 'local_coodle_advisor';
        } else if ($mode == COODLEUSER) {
            $this->table = 'local_coodle_user';
        }
    }

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
     * Check if user is a Coodle advisor
     *
     * @param int $userid
     * @return boolean
     */
    public static function is_user($userid = 0) : bool {
        global $DB, $USER;
        if (empty($userid)) {
            $userid = $USER->id;
        }
        return $DB->record_exists('local_coodle_user', array('userid' => $userid));
    }

    /**
     * Deletes an advisor fomr coodle table
     */
    public function delete_advisor($userid) {
        global $DB;
        return $DB->delete_records('local_coodle_advisor', array('userid' => $userid));
    }

    /**
     * Deletes an advisor fomr coodle table
     */
    public function delete_coodle_user($userid) {
        global $DB;
        return $DB->delete_records('local_coodle_user', array('userid' => $userid));
    }

    /**
     * Load user
     *
     * @param integer $userid
     * @return stdClass $coodleuser
     */
    public function load_user(int $userid = null) {
        global $DB, $USER;
        if (empty($userid)) {
            $userid = $USER->id;
        }
        $user = $DB->get_record($this->table, array('userid' => $userid));
        $this->token = $user->token;
        $this->id = $user->id;
        $this->userid = $user->userid;
        if (empty($user->token)) {
            $user->token = $this->renew_token();
            $this->token = $user->token;
        }

        return $user;
    }

    /**
     * Generate a new token
     *
     * @return string $token
     */
    public static function generate_coodle_token() {
        $token = bin2hex(random_bytes(32));
        return $token;
    }

    /**
     * Return usertoken
     *
     * @return string
     */
    public function get_token() {
        return $this->token;
    }

    /**
     *
     * @return void
     * @throws Exception
     */
    public function renew_token() {
        global $DB;
        $params = array(
            'tokencreated' => time(),
            'token' => self::generate_coodle_token(),
            'id' => $this->id
        );
        $DB->update_record($this->table, $params);
        return $params['token'];
    }

    /**
     * Compares token with usertoken.
     * @param string $token
     * @return bool
     */
    public function compare_token(string $token) {
        if ($this->token == $token) {
            return true;
        }
        return false;
    }
}

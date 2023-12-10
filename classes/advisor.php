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
 * Class to handle coodle advisors.
 *
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coodle;

use stdClass;

/**
 * Class advisor.
 *
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class advisor {

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
    public $courseid;

    /**
     * Settings of Advisor
     *
     * @var string
     */
    public $settings;

    /**
     * Timecreated
     *
     * @var int
     */
    public $timecreated;

    /**
     * Timemodified
     *
     * @var int
     */
    public $timemodified;

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $token;

    public $tokencreated;

    /**
     * Variable if advisor is deleted
     *
     * @var int
     */
    public $deleted;

    /**
     * Whole data
     *
     * @var array
     */
    protected $data;

    /**
     * Undocumented function
     *
     * @param  integer $userid
     * @param  integer $courseid
     * @param  boolean $createadvisor
     */
    public function __construct(int $userid, int $courseid = 0, bool $createadvisor = false) {
        global $DB;
        if ($createadvisor) {
            $token = \local_coodle\settings_manager::generate_coodle_token();
            $data = ['userid' => $userid, 'courseid' => $courseid, 'timecreated' => time(), 'token' => $token];
            $advisorid = $DB->insert_record('local_coodle_advisor', $data, true);
            $this->data = $data;
            $this->data['id'] = $advisorid;
            $this->set_coodle_preferences($userid);
            $this->enrol_advisor_to_advisorcourse($userid);
        }
        $this->userid = $userid;
        $this->courseid = $courseid;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function get_advisor() {
        global $DB;
        $record = $DB->get_record('local_coodle_advisor', ['userid' => $this->userid]);
        $this->data = $record;
        $this->timecreated = $record->timecreated;
        $this->token = $record->token;
        $this->tokencreated = $record->tokencreated;
        $this->courseid = $record->courseid;
        $this->settings = $record->settings;
        return $this;
    }

    /**
     * Creates a course with the name of the advisor
     *
     * @param int $userid of advisor
     * @return int
     */
    public static function create_course_for_adivisor($userid) {
        global $CFG, $DB;
        $userdata = \core_user::get_user($userid);
        $data = new stdClass();
        $data->fullname = $userdata->firstname . '_'  . $userdata->lastname;
        $data->shortname = $userdata->username . '_'  . $userdata->id;
        $data->idnumber = $userdata->username  . ' ' . $userdata->id;
        // Enable seperate groups.
        $data->groupmode = 1;
        $data->category = \local_coodle\settings_manager::create_or_get_standard_coursecategory();
        require_once($CFG->dirroot.'/course/lib.php');
        $i = 1;
        while ($DB->record_exists('course', ['shortname' => $data->shortname]) || $DB->record_exists('course', ['shortname' => $data->id])) {
            $data->shortname = $data->shortname . "($i)";
            $data->idnumber = $data->idnumber . "($i)";
            $i++;
        }
        $course = create_course($data);
        self::course_manual_enrolments([$course->id], [$userid], 3);
        return $course->id;
    }

    /**
     * Returns the personal advisor course from userid of advisor
     *
     * @param int $advisorid
     * @return int
     */
    public static function get_courseid_from_advisorid(int $advisorid) {
        global $DB;
        $record = $DB->get_record('local_coodle_advisor', ['userid' => $advisorid], 'courseid', IGNORE_MISSING);
        return $record->courseid;
    }

    /**
     * External function used in WS create_group_for_advisor
     * Creates a group for advisor + user
     *
     * @param int $advisorid
     * @param int $customerid
     * @return void
     */
    public static function create_group_for_advisor(int $advisorid, int $userid) {
        global $CFG, $DB;
        $courseid = self::get_courseid_from_advisorid($advisorid);
        $user = \core_user::get_user($userid);
        $coursectx = \context_course::instance($courseid);
        if (!empty($coursectx->id)) {
            if (!is_enrolled($coursectx, $user, '', true)) {
                self::course_manual_enrolments([$courseid], [$userid], 5);
            }
            require_once("$CFG->dirroot/group/lib.php");
            $groupname = fullname($user) . ' (' . $user->id . ')';
            $group = $DB->get_record('groups', ['courseid' => $courseid, 'name' => $groupname]);
            if (empty($group->id)) {
                // Create a group for this user.
                $group = (object) [
                    'courseid' => $courseid,
                    'name' => $groupname,
                    'description' => '',
                    'descriptionformat' => 1,
                    'timecreated' => time(),
                    'timemodified' => time(),
                ];
                $group->id = groups_create_group($group, false);
            }
            if (!empty($group->id)) {
                groups_add_member($group, $advisorid);
                groups_add_member($group, $userid);
            }
        }
    }

    /**
     * Enrols users to specific courses
     *
     * @param array $courseids containing courseids or a single courseid
     * @param array $userids containing userids or a single userid
     * @param int $roleid roleid to assign, or -1 if wants to unenrolroleid to assign, or -1 if wants to unenrol
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function course_manual_enrolments(array $courseids, array $userids, int $roleid): bool {
        global $DB;
        if (!is_array($courseids)) {
            $courseids = [$courseids];
        }
        if (!is_array($userids)) {
            $userids = [$userids];
        }

        // Check manual enrolment plugin instance is enabled/exist.
        $enrol = enrol_get_plugin('manual');
        if (empty($enrol)) {
            throw new \moodle_exception('manualpluginnotinstalled', 'enrol_manual');
        }
        $instances = [];
        foreach ($courseids as $courseid) {
            // Check if course exists.
            $course = $DB->get_record('course', ['id' => $courseid], '*', IGNORE_MISSING);
            if (empty($course->id)) {
                continue;
            }
            if (empty($instances[$courseid])) {
                $instances[$courseid] = self::get_enrol_instance($courseid);
            }

            foreach ($userids as $userid) {
                $user = $DB->get_record('user', ['id' => $userid]);
                if (empty($user->id)) {
                    continue;
                }
                if ($roleid == -1) {
                    $enrol->unenrol_user($instances[$courseid], $userid);
                } else {
                    $enrol->enrol_user($instances[$courseid], $userid, $roleid, time(), 0, ENROL_USER_ACTIVE);
                }
            }
        }
        return true;
    }

    /**
     * Get the enrol instance for manual enrolments of a course, or create one.
     *
     * @param courseid
     * @return object enrolinstance
     */
    private static function get_enrol_instance($courseid) {
        // Check manual enrolment plugin instance is enabled/exist.
        $enrol = enrol_get_plugin('manual');
        if (empty($enrol)) {
            throw new \moodle_exception('manualpluginnotinstalled', 'enrol_manual');
        }
        $instance = null;
        $enrolinstances = enrol_get_instances($courseid, false);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                return $courseenrolinstance;
            }
        }
        if (empty($instance)) {
            $course = get_course($courseid);
            $enrol->add_default_instance($course);
            return self::get_enrol_instance($courseid);
        }
    }

    /**
     * Gets all coodle advisors with user data from MOODLE user table
     *
     * @return array
     */
    public static function get_coodle_advisors() {
        global $DB;
        $sql = "SELECT *, ca.timecreated as timecreated FROM {local_coodle_advisor} ca JOIN {user} u on ca.userid = u.id";
        $data = $DB->get_records_sql($sql);
        return $data;
    }

    /**
     * Prepare advisors for template
     *
     * @return array templatedata
     */
    public static function prepare_for_template() {
        $coodleadvisors = self::get_coodle_advisors();
        $templatedata = [];
        foreach ($coodleadvisors as $coodleadvisor) {
            $tdata = $coodleadvisor;
            $tdata->advisordatecreated = date("Y-m-d", $coodleadvisor->timecreated);
            $templatedata[] = $tdata;
        }
        return $templatedata;
    }

    /**
     * Get advisorcourse
     *
     * @param int $advisorid
     * @return int courseid
     */
    public static function get_advisor_course(int $advisorid) {
        global $DB;
        $record = $DB->get_record('local_coodle_advisor', ['userid' => $advisorid], 'courseid');
        return $record->courseid;
    }

    /**
     * Output data for advisor adresscard
     *
     * @param int $advisorid
     * @return stdClass
     */
    public static function get_advisor_addrescard(int $advisorid) {
        global $DB;
        $record = $DB->get_record('local_coodle_advisor', ['userid' => $advisorid], 'settings');
        if (!empty($record->settings)) {
            $settings = json_decode($record->settings);
        }
        return $settings;
    }

    /**
     * get_advisor_list
     *
     * @return array
     */
    public static function get_advisor_list() {
        $users = self::get_coodle_advisors();
        if (isset($users)) {
            foreach ($users as $user) {
                $id = $user->id;
                $userlist[$id] = $user->firstname . ' ' . $user->lastname;
            }
            return $userlist;
        }
        return [];
    }

    public static function set_coodle_advisor($data) {
        global $DB;
        if ($DB->update_record('local_coodle_user', $data)) {
            $record = $DB->get_record('local_coodle_user', ['id' => $data->id]);
            self::create_group_for_advisor($record->advisorid, $record->userid);
        }
    }

    /**
     * Check is user has advisor xy
     *
     * @param  int  $userid
     * @param  int  $advisorid
     *
     * @return boolean
     */
    public static function is_advisor_from($userid, $advisorid): bool {
        global $DB;
        return $DB->record_exists('local_coodle_user', ['userid' => $userid, 'advisorid' => $advisorid]);
    }

    /**
     * Checks if user is advisor
     *
     * @param integer $userid
     * @return boolean
     */
    public static function is_advisor(int $userid = 0) {
        global $DB, $USER;
        if (empty($userid)) {
            $userid = $USER->id;
        }
        return $DB->record_exists('local_coodle_advisor', ['userid' => $userid]);
    }

    /**
     * Create calendar entry
     */
    public function create_calendar_entry($advisorid, $userid, $eventdata) {
        require_once($CFG->dirroot.'/calendar/lib.php');

        $event = new stdClass();
        $event = $eventdata;
        $event->eventtype = 'user';
        $event->type = CALENDAR_EVENT_TYPE_STANDARD; // Or: $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->name = "adsadasd";

        $event->description = "asdadasd";
        $event->format = FORMAT_HTML;
        $event->courseid = 0;
        $event->categoryid = 0;
        $event->userid = $userid;
        // Check some capability
        \calendar_event::create($event, false);

        $event->userid = $advisorid;
        \calendar_event::create($event, false);

        // TODO maybe create group event?

    }

    /**
     * Counts all advisors in $DB
     *
     * @return void
     */
    public static function count_advisors() {
        global $DB;
        return $DB->count_records('local_coodle_advisor', null);
    }

    /**
     * Set User preferencese
     * @param int $userid
     */
    public function set_coodle_preferences(int $userid) {
        $value = '{"isadvisor":true, "isuser":false}';
        set_user_preferences(['coodle_settings' => $value], $userid);
    }


    /**
     * Delete Advisor
     * @param int $userid - the id of the advisor
     */
    public static function delete(int $userid) {
        global $DB;

        $record = $DB->get_record('local_coodle_advisor', ['userid' => $userid]);
        $DB->delete_records('local_coodle_advisor', ['userid' => $userid]);
        // SQL to remove the advisors from the coodle users.
        $sql = "
        UPDATE {local_coodle_user}
        SET advisorid = NULL
        WHERE advisorid = :advisorid";

        $params = ['advisorid' => $userid];
        // Reset user prefs.
        set_user_preferences(['coodle_settings' => 'null'], $userid);

        // Delete course from advisor.
        delete_course($record->courseid);
        // Execute the SQL query
        $DB->execute($sql, $params);
    }

    public function enrol_advisor_to_advisorcourse($userid) {
        $advisorcourse = get_config('local_coodle', 'coodleadvisorcourseid');
        if ($advisorcourse) {
            self::course_manual_enrolments([$advisorcourse], [$userid], 5);
        }
    }
}

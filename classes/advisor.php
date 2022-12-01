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
 * Class advisor.
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

    protected $data;

    public function __construct(int $userid, int $courseid = 0, bool $createadvisor) {
        global $DB;
        if ($createadvisor) {
            $data = ['userid' => $userid, 'courseid' => $courseid, 'timecreated' => time()];
            $advisorid = $DB->insert_record('local_coodle_advisor', $data, true);
            $this->data = $data;
            $this->data['id'] = $advisorid;
        }
        $this->userid = $userid;
        $this->courseid = $courseid;

    }

    /**
     * Creates a course with the name of the advisor
     *
     * @param int $userid of advisor
     * @return int
     */
    public static function create_course_for_adivisor($userid) {
        global $CFG;
        $userdata = \core_user::get_user($userid);
        $data = new stdClass();
        $data->fullname = $userdata->firstname . '_'  . $userdata->lastname;
        $data->shortname = $userdata->username . '_'  . $userdata->id;
        $data->idnumber = $userdata->username  . ' ' . $userdata->id;
        $data->category = \local_coodle\settings_manager::create_or_get_standard_coursecategory();
        require_once($CFG->dirroot.'/course/lib.php');
        $course = create_course($data);
        return $course->id;
    }

    /**
     * Returns the personal advisor course from userid of advisor
     *
     * @param int $advisorid
     * @return int
     */
    public static function get_courseid_from_advisorid(int $advisorid) : int {
        global $DB;
        $courseid = $DB->get_record('local_coodle_advisor', ['userid' => $advisorid], 'courseid', IGNORE_MISSING);
        return $courseid;
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
        $courseid = self::get_courseid_from_advisorid($advisorid);
        $user = \core_user::get_user($userid);
        $coursectx = \context_course::instance($courseid);
        if (!empty($coursectx->id)) {
            if (!is_enrolled($coursectx, $user, '', true)) {
                self::course_manual_enrolments(array($courseid), array($userid), 5);
            }
            require_once("$CFG->dirroot/group/lib.php");
            $groupname = fullname($user) . ' (' . $user->id . ')';
            $group = $DB->get_record('groups', array('courseid' => $forum->course, 'name' => $groupname));
            if (empty($group->id)) {
                // create a group for this user.
                $group = (object) array(
                    'courseid' => $forum->course,
                    'name' => $groupname,
                    'description' => '',
                    'descriptionformat' => 1,
                    'timecreated' => time(),
                    'timemodified' => time(),
                );
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
            $courseids = array($courseids);
        }
        if (!is_array($userids)) {
            $userids = array($userids);
        }

        // Check manual enrolment plugin instance is enabled/exist.
        $enrol = enrol_get_plugin('manual');
        if (empty($enrol)) {
            throw new \moodle_exception('manualpluginnotinstalled', 'enrol_manual');
        }
        $instances = array();
        foreach ($courseids as $courseid) {
            // Check if course exists.
            $course = $DB->get_record('course', array('id' => $courseid), '*', IGNORE_MISSING);
            if (empty($course->id)) {
                continue;
            }
            if (empty($instances[$courseid])) {
                $instances[$courseid] = self::get_enrol_instance($courseid);
            }

            foreach ($userids as $userid) {
                $user = $DB->get_record('user', array('id' => $userid));
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
        $sql = "SELECT * FROM {local_coodle_advisor} ca JOIN {user} u on ca.userid = u.id";
        $data = $DB->get_records_sql($sql);
        return $data;
    }

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
     * Counts all advisors in $DB
     *
     * @return void
     */
    public static function count_advisors() {
        global $DB;
        return $DB->count_records('local_coodle_advisor', null);
    }
}

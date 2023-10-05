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
 * This class contains a list of webservice functions related to the Shopping Cart Module by Wunderbyte.
 *
 * @package    local_coodle
 * @copyright  2022 Thomas Winkler <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_coodle\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_warnings;
use external_format_value;
use \core_calendar\local\event\container as event_container;
use \core_calendar\local\event\forms\create as create_event_form;
use \core_calendar\local\event\forms\update as update_event_form;
use \core_calendar\local\event\mappers\create_update_form_mapper;
use \core_calendar\external\event_exporter;
use \core_calendar\external\events_exporter;
use \core_calendar\external\events_grouped_by_course_exporter;
use \core_calendar\external\events_related_objects_cache;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class get_calendar_events extends external_api {

    /**
     * Describes the paramters for add_advisor.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() : external_function_parameters {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'userid', VALUE_REQUIRED),
            )
        );
    }
    /**
     * Webservice to create an advisor.
     *
     * @return array
     */
    public static function execute(int $userid): array {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/calendar/lib.php');

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), array('userid' => $userid));
        $warnings = array();
        $coodleusersettings = get_user_preferences('coodle_settings');
        if ($coodleusersettings) {
            $coodleusersettings = json_decode($coodleusersettings);
        }

        $userchosen = (get_user_preferences('coodleuser_chosen'));
        if ($userchosen) {
            $userchosen = json_decode($userchosen);
        }
        if ($coodleusersettings && $coodleusersettings->isadvisor) {
            if ($userchosen && $userchosen->userid) {
                $userid = $userchosen->userid;
            }
        } else {
            $userid = $USER->id;
        }
        $cu = new \local_coodle\coodle_user();
        $cu->load_user($userid);
        $courseid = \local_coodle\advisor::get_courseid_from_advisorid((int)$cu->advisorid);
        $groups = groups_get_user_groups($courseid, $userid);
        $eventlist = calendar_get_legacy_events(strtotime("-1 day"), strtotime("+1 month"),
        $userid, $groups[0][0], $courseid, true,
        $params['options']['ignorehidden']);
        $events = $eventlist;
        return array('events' => $events, 'warnings' => $warnings);
    }

    /*
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(array(
            'events' => new external_multiple_structure( new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'event id'),
                        'name' => new external_value(PARAM_RAW, 'event name'),
                        'description' => new external_value(PARAM_RAW, 'Description', VALUE_OPTIONAL, null, NULL_ALLOWED),
                        'format' => new external_format_value('description'),
                        'courseid' => new external_value(PARAM_INT, 'course id'),
                        'categoryid' => new external_value(PARAM_INT, 'Category id (only for category events).',
                            VALUE_OPTIONAL),
                        'groupid' => new external_value(PARAM_INT, 'group id'),
                        'userid' => new external_value(PARAM_INT, 'user id'),
                        'repeatid' => new external_value(PARAM_INT, 'repeat id'),
                        'modulename' => new external_value(PARAM_TEXT, 'module name', VALUE_OPTIONAL, null, NULL_ALLOWED),
                        'instance' => new external_value(PARAM_INT, 'instance id'),
                        'eventtype' => new external_value(PARAM_TEXT, 'Event type'),
                        'timestart' => new external_value(PARAM_INT, 'timestart'),
                        'timeduration' => new external_value(PARAM_INT, 'time duration'),
                        'visible' => new external_value(PARAM_INT, 'visible'),
                        'uuid' => new external_value(PARAM_TEXT, 'unique id of ical events', VALUE_OPTIONAL, null, NULL_NOT_ALLOWED),
                        'sequence' => new external_value(PARAM_INT, 'sequence'),
                        'timemodified' => new external_value(PARAM_INT, 'time modified'),
                        'subscriptionid' => new external_value(PARAM_INT, 'Subscription id', VALUE_OPTIONAL, null, NULL_ALLOWED),
                    ), 'event')
             ),
             'warnings' => new external_warnings()
            )
        );
    }
}

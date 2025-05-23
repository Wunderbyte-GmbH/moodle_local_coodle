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
 * Local cohorts module capability definition
 *
 * @package         local_coodle
 * @author          Christian Badusch, Thomas Winkler
 * @copyright       2022 Wunderbyte GmbH
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace local_coodle\output;

use local_coodle\coodle_user;
use local_coodle\settings_manager;
use stdClass;

class mobile {

    /**
     * Returns the javascript needed to initialize some Handlers
     *
     * @return array javascript
     */
    public static function mobile_init() {
        global $CFG;

        return [
            'templates' => [],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/init.js"),
        ];
    }

    /**
     * Returns the view for "Dokumente"
     *
     * @return array mobiletemplatedata
     */
    public static function view_files1() {
        global $USER, $OUTPUT, $CFG;
        $coodleuser = new coodle_user();
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $template = 'local_coodle/mobile_todos';
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));

        if ($coodleusersettings->isadvisor) {
            if ($userchosen->userid) {
                $userid = $userchosen->userid;
            } else {
                $userid = $USER->id;
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }
        $coodleuser->load_user($userid);
        $templatedata = new stdClass();
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(1);

        $templatedata->header = get_string('docsdesc', 'local_coodle');
        $templatedata->nodata = (!empty($templatedata->otherfiles)) ? 0 : 1;
        $templatedata->nodatastring = get_string('docsdesc', 'local_coodle');

        $templatedata->text = "#fff";
        $templatedata->hl = "Dokumente";
        // Green.
        $templatedata->bg = "#669933";
        $templatedata->text = "#fff";

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_files', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * view_files2
     *
     * @return array mobiletemplatedata
     */
    public static function view_files2() {
        global $USER, $OUTPUT, $CFG;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(2);
        $templatedata->hl = "Bewerbungsunterlagen";
        $templatedata->bg = "#0f47ad";
        $templatedata->text = "#fff";

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_files', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * view_files3
     *
     * @return array mobiletemplatedata
     */
    public static function view_files3() {
        global $USER, $OUTPUT, $CFG;
        $coodleuser = new coodle_user();
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));

        if ($coodleusersettings->isadvisor) {
            if($userchosen->userid) {
                $userid = $userchosen->userid;
            } else {
                $userid = $USER->id;
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }
        $coodleuser->load_user($userid);
        $templatedata = new stdClass();
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(3);
        $templatedata->header = get_string('docsadvdesc', 'local_coodle');
        $templatedata->nodata = (!empty($templatedata->otherfiles)) ? 0 : 1;
        $templatedata->nodatastring = get_string('nodocsadv', 'local_coodle');

        $templatedata->bg = "rgb(102, 153, 204)";
        $templatedata->text = "#fff";
        $links = new \local_coodle\coodle_link();
        $linklist = $links->load_linklist_by_userid($userid);
        $templatedata->links = $linklist;
        $templatedata->adresscard = \local_coodle\advisor::get_advisor_addrescard($coodleuser->advisorid);

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_beratung', $templatedata),
                    'cache-view' => false
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * View adress page on mobile
     *
     * @return array mobiletemplatedata
     */
    public static function view_address() {
        global $OUTPUT, $USER;

        $coodleuser = new coodle_user();
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));

        if ($coodleusersettings->isadvisor) {
            if ($userchosen->userid) {
                $userid = $userchosen->userid;
            } else {
                $userid = $USER->id;
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }
        $coodleuser->load_user($userid);

        $templatedata = new stdClass();
        // Address Purple.
        $templatedata->bg = "rgb(102, 68, 153)";
        $templatedata->adresses = $coodleuser->get_coodleuser_directions($userid);
        $templatedata->header = get_string('directionsdesc', 'local_coodle');
        $templatedata->nodata = (is_array($templatedata->adresses) && !empty($templatedata->adresses)) ? 0 : 1;
        $templatedata->nodatastring = get_string('nodirections', 'local_coodle');

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_address_view', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * Not needed!
     *
     * @return array mobiletemplatedata
     */
    public static function view_faq() {
        global $USER, $OUTPUT;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(4);
        $templatedata->test = "Test";

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_test', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * Not needed!
     *
     * @return void
     */
    public static function view_impressum() {
        global $USER, $OUTPUT, $CFG;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $pixurl = $CFG->wwwroot."/local/coodle/pix/";
        $templatedata->pixurl = $pixurl;
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_impressum', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * View Dates
     *
     * @return array mobiletemplatedata
     */
    public static function view_dates() {
        global $USER, $OUTPUT;
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));

        if ($coodleusersettings->isadvisor) {
            if ($userchosen->userid) {
                $userid = $userchosen->userid;
            } else {
                $userid = $USER->id;
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(224, 102, 0)";

        $events = \local_coodle\external\get_calendar_events::execute($userid);
        $templatedata->events = [];
        foreach($events['events'] as $event) {
            $templatedata->events[] = [
                'name' => $event->name,
                'location' => $event->location,
                'timestart' => date("d.m H:i", $event->timestart),
                'description' => $event->description,
            ];
        }
        $templatedata->header = get_string('datesdesc', 'local_coodle');
        $templatedata->nodata = (is_array($templatedata->events) && !empty($templatedata->events)) ? 0 : 1;
        $templatedata->nodatastring = get_string('nodates', 'local_coodle');

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_dates', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * View Todos on Mobile
     *
     * @return array mobiletemplatedata
     */
    public static function view_todos() {
        global $USER, $OUTPUT, $CFG;
        $todo = new \local_coodle\todo();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(204, 0, 51)";
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));
        $todolist = [];
        if ($coodleusersettings->isadvisor) {
            if($userchosen) {
                $userid = $userchosen->userid;
            } else {
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }

        $todolist = $todo->load_todolist_by_userid($userid, 1);

        // TODO: change stats!
        if (empty($todolist['open'])) {
            $templatedata->empty = 1;
        }

        $templatedata->todosopen = $todolist['open'];
        $templatedata->todosdone = $todolist['done'];
        $templatedata->userid = $userid;

        $templatedata->header = get_string('todosdesc', 'local_coodle');
        $templatedata->nodata = (is_array($templatedata->todosopen) && !empty($templatedata->todosopen)) ? 0 : 1;
        $templatedata->nodatastring = get_string('notodos', 'local_coodle');

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_todos', $templatedata),
                    'cache-view' => false
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/modal.js"),
            'otherdata' => ['showmodal' => 0],
        ];
    }

    /**
     * View Info Page on Mobile
     *
     * @return array mobiletemplatedata
     */
    public static function view_info() {
        global $USER, $OUTPUT;

        // TODO: change and write functions!
        $links = new \local_coodle\coodle_link();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(251, 135, 66)";
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));
        if ($coodleusersettings->isadvisor) {
            if ($userchosen) {
                $userid = $userchosen->userid;
            } else {
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }

        $coodleuser = new \local_coodle\coodle_user();
        $coodleuser->load_user($userid);
        $templatedata->adresscard = \local_coodle\advisor::get_advisor_addrescard($coodleuser->advisorid);

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_linklist', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * User Selection for coodle advisors
     *
     * @return array mobiletemplatedata
     */
    public static function select_user() {
        global $USER, $OUTPUT, $CFG;
        // TODO: change and write functions!
        $userchosen = json_decode(get_user_preferences('coodleuser_chosen'));

        if (\local_coodle\settings_manager::is_advisor()) {
            $users = \local_coodle\coodle_user::get_coodle_users($USER->id, 1);
            $users = array_values($users);
        }

        if ($userchosen->userid > 0) {
            $user = \local_coodle\coodle_user::get_coodle_user($userchosen->userid);
        }

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile/mobile_select_user', ['users' => $users, 'userchosen' => $user]),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/syncData.js"),
            'otherdata' => '',
        ];
    }

    /**
     * User Selection for coodle advisors
     *
     * @return array mobiletemplatedata
     */
    public static function view_test() {
        global $USER, $OUTPUT, $CFG;
        // TODO: change and write functions!
        $templatedata = new stdClass();

        $events = \local_coodle\external\get_calendar_events::execute($USER->id);
        $templatedata->events = [];
        foreach ($events['events'] as $event) {
            $templatedata->events[] = [
                'name' => $event->name,
                'timestart' => date("d.m H:i", $event->timestart),
            ];
        }

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_test', $templatedata),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/modal.js"),
            'otherdata' => ['showmodal' => 0],
        ];
    }
}

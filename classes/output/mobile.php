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
        $userchosen = get_user_preferences('coodleuser_chosen');

        if ($coodleusersettings->isadvisor) {
            if($userchosen) {
                $userid = $userchosen;
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
        $templatedata->hl = "Dokumente";
        $templatedata->bg = "rgb(94, 160, 242)";
        $templatedata->text = "#fff";


        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_files', $templatedata),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/test.js"),
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
        $js = 'const uploadFile = () => { console.log("hijo");}';

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_files', $templatedata),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/test.js"),
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
        $template = 'local_coodle/mobile_todos';
        $userchosen = get_user_preferences('coodleuser_chosen');

        if ($coodleusersettings->isadvisor) {
            if($userchosen) {
                $userid = $userchosen;
            } else {
                $userid = $USER->id;
                $template = 'local_coodle/mobile_nouserchosen';
            }
        } else {
            $userid = $USER->id;
        }
        $coodleuser->load_user($userid);
        $templatedata = new stdClass();
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(3);
        $templatedata->hl = "Beratungsinhalt";
        $templatedata->bg = "rgb(33, 181, 98)";
        $templatedata->text = "#fff";
        $js = 'const uploadFile = () => { console.log("hijo");}';

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_files', $templatedata),
                    'cache-view' => false
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/test.js"),
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
        $template = 'local_coodle/mobile_todos';
        $userchosen = get_user_preferences('coodleuser_chosen');

        if ($coodleusersettings->isadvisor) {
            if ($userchosen) {
                $userid = $userchosen;
            } else {
                $userid = $USER->id;
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }
        $coodleuser->load_user($userid);

        $templatedata = new stdClass();
        $templatedata->bg = "rgb(163, 96, 239)";
        $templatedata->adresses = $coodleuser->get_coodleuser_directions($userid);

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_address_view', $templatedata),
                ],
            ],
            'javascript' => 'export class ExampleComponent {
                isModalOpen = false;
                setOpen(isOpen: boolean) {
                  this.isModalOpen = isOpen;
                }
              }'
                ,
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
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_test', $templatedata),
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
    public static function view_dates() {
        global $USER, $OUTPUT;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/get_coodleuser_dates', $templatedata),
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
        global $USER, $OUTPUT;
        $todo = new \local_coodle\todo();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(238, 58, 47)";
        $coodleusersettings = json_decode(get_user_preferences('coodle_settings'));
        $userchosen = get_user_preferences('coodleuser_chosen');
        $todolist = [];
        $template = 'local_coodle/mobile_todos';
        if ($coodleusersettings->isadvisor) {
            if($userchosen) {
                $userid = $userchosen;
            } else {
                return self::select_user();
            }
        } else {
            $userid = $USER->id;
        }

        $todolist = $todo->load_todolist_by_userid($userid, 0);

        // TODO: change stats!
        if (!empty($todolist)) {
            $templatedata->todos = $todolist;
        } else {
            $templatedata->empty = 1;
        }
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template($template, $templatedata),
                    'cache-view' => false
                ],
            ],
            'javascript' => 'setTimeout(function() { console.log("DOM is available now"); this.refreshContent(true); });',
            'otherdata' => '',
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
        $links = new \local_coodle\link();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(251, 135, 66)";
        $linklist = $links->load_linklist_by_userid($USER->id);
        $templatedata->links = $linklist;
        $coodleuser = new \local_coodle\coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata->adresscard = \local_coodle\advisor::get_advisor_addrescard($coodleuser->advisorid);

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_linklist', $templatedata),
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
        global $USER, $OUTPUT;
        // TODO: change and write functions!
        if (\local_coodle\settings_manager::is_advisor()) {
            $users = \local_coodle\coodle_user::get_coodle_users($USER->id);
            $users = array_values($users);
        }

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_select_user', ['users' => $users]),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }
}

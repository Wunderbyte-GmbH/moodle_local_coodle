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
     *
     */
    public static function view_files1() {
        global $USER, $OUTPUT, $CFG;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
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
     */
    public static function view_files3() {
        global $USER, $OUTPUT, $CFG;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
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
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . "/local/coodle/mobile/js/test.js"),
            'otherdata' => '',
        ];
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function view_address() {
        global $OUTPUT;
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(163, 96, 239)";
        $templatedata->adresses[0] = ['content'=>'asdhasjkdhaskjd', 'title' => 'test'];
        $templatedata->adresses[1] = ['content'=>'asdasdwqeqwe','title' => 'test2'];
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
     * Undocumented function
     *
     * @return void
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
     * Undocumented function
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
     * Undocumented function
     *
     * @return void
     */
    public static function view_todos() {
        global $USER, $OUTPUT;
        $todo = new \local_coodle\todo();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(238, 58, 47)";
        // TODO: change stats!
        $todolist = $todo->load_todolist_by_userid($USER->id, 0);
        if (!empty($todolist)) {
            $templatedata->todos = $todolist;
        } else {
            $templatedata->empty = 1;
        }
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_todos', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function view_info() {
        global $USER, $OUTPUT;
        // TODO: change and write functions!
        $links = new \local_coodle\link();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(251, 135, 66)";
        $linklist = $links->load_linklist_by_userid($USER->id);
        $templatedata->links = $linklist;

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
     * Undocumented function
     *
     * @return void
     */
    public static function select_user() {
        global $USER, $OUTPUT;
        // TODO: change and write functions!
        $links = new \local_coodle\link();
        $templatedata = new stdClass();
        $templatedata->bg = "rgb(251, 135, 66)";
        $linklist = $links->load_linklist_by_userid($USER->id);
        $templatedata->links = $linklist;

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_select_user', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }
}

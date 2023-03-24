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
 * @author          Christian Badusch, Thomsa Winkler
 * @copyright       2022 Wunderbyte GmbH
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace local_coodle\output;

use local_coodle\coodle_user;
use stdClass;

class mobile {

    public static function view_files1($args) {
        global $OUTPUT, $USER;
        $templatedata = new stdClass();
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(1);
        $templatedata->title = 'Dokumente';
        $templatedata->bg = "#64a44e";

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_files', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    public static function view_files2($args) {
        global $USER, $OUTPUT;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $templatedata->title = 'Bewerbungsunterlagen';
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(2);

       // $templatedata->hl = $args['userid'];
        $templatedata->bg = "#0f47ad";

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_files', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    public static function view_files3($args) {
        global $USER, $OUTPUT;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $templatedata->title = 'Beratungsinhalt';
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files(3);

        // $templatedata->hl = $args['userid'];
        $templatedata->bg = "#ced4da";

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_files', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    public static function view_address() {
        global $OUTPUT;
        $templatedata = new stdClass();
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('local_coodle/mobile_address_view', $templatedata),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
        ];
    }

    public static function view_faq() {
        global $USER, $OUTPUT;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $templatedata->otherfiles = $coodleuser->get_coodleuser_files();
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
}
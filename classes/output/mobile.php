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
 * @author          Christian Badusch
 * @copyright       2022 Wunderbyte GmbH
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace local_coodle\output;

use local_coodle\coodle_user;
use stdClass;

class mobile {

    public static function view_hello() {
        global $USER, $OUTPUT;
        $coodleuser = new coodle_user();
        $coodleuser->load_user($USER->id);
        $templatedata = new stdClass();
        $templatedata->files = $coodleuser->get_coodleuser_files();

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

}
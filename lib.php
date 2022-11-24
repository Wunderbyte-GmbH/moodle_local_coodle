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
 * Local coodle lib hooks
 * @package    local_coodle
 * @copyright  2021 Wunderbyte GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renders the popup.
 *
 * @param renderer_base $renderer
 * @return string The HTML
 */
function local_coodle_render_navbar_output(\renderer_base $renderer) {

    // Early bail out conditions.
    if (!isloggedin() || isguestuser() || !has_capability('local/musi:canedit', context_system::instance())) {
        return;
    }

    global $OUTPUT;
    $templatedata = array('image' => $OUTPUT->image_url('coodle', 'local_coodle'));
    $nav = $OUTPUT->render_from_template('local_coodle/navicon', $templatedata);
    return $nav;
}

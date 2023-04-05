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
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

// TODO delete before production

$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

// $repository = repository::get_instance(9);

// Get the context
$context = context_user::instance($USER->id);

$testfiles = external_util::get_area_files($context->id, 'local_coodle', 'clientfile', false, false);
$browser = get_file_browser();
$fileinfo = $browser->get_file_info($context->id, 'local_coodle', 'clientfile');


// Loop through the file list and display the file names
// Loop through the file list and display the file names and contents

echo $OUTPUT->footer();

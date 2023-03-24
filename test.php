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

$repository = repository::get_instance(9);

// Get the context
$context = context_system::instance();

// Loop through the file list and display the file names
// Loop through the file list and display the file names and contents
$file_list = $repository->get_listing($context->id, 'repository', 'file', '/', '', true);

// Loop through the file list and display the file names and contents
foreach ($file_list['list'] as $file) {
    // Get the file contents
    $file = $repository->get_file_by_id($file_id);

    // Output the file name and contents
    echo $file->get_filename() . " contents:<br>";
    echo $file_contents . "<br>";
}

echo $OUTPUT->footer();

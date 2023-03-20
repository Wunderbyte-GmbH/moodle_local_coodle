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

use local_coodle\coodle_user;
use local_coodle\local\views\secondary;

$delid = optional_param('del', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

$secondarynav = new secondary($PAGE);
$secondarynav->initialise();
$PAGE->set_secondarynav($secondarynav);
$PAGE->set_secondary_navigation(true);

$PAGE->set_url(new moodle_url('/local/coodle/advisorlist.php', array()));
$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();


global $CFG, $USER, $DB;
$context = context_system::instance();

// Get the file storage instance


$coodleuser = new coodle_user();
$coodleuser->load_user(33);

$context = \context_system::instance();

// Get the file storage instance
$filestorage = get_file_storage();

// Get all files from the file storage
$files = $filestorage->get_area_files($context->id, 'local_coodle', 'clientfilestemp', false);

foreach ($files as $file) {

    $fs = get_file_storage();
    if ($file->get_filename() != '.') {

        $filerecord = [
        'contextid'    => $file->get_contextid(),
        'component'    => $file->get_component(),
        'filearea'     => 'newfilearea',
        'itemid'       => 0,
        'filepath'     => '/33/',
        'filename'     => $file->get_filename(),
        'timecreated'  => time(),
        'timemodified' => time(),
        ];


        $fs->create_file_from_storedfile($filerecord, $file);

        // Now delete the original file.
        $file->delete();
    }
}






echo $OUTPUT->footer();

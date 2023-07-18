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


use core_files\archive_writer;
use ZipArchive;
require_once('../../config.php');

$context = \context_system::instance();
$PAGE->set_context($context);

require_login();

// TODO delete before production.

$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);

if (\local_coodle\settings_manager::is_advisor()) {
    $out = array();
    $users =  \local_coodle\coodle_user::get_coodle_users($USER->id);
    $users = array_values($users);
}

global $USER, $OUTPUT;

// TODO: change and write functions!
$links = new \local_coodle\link();
$templatedata = new stdClass();
$templatedata->bg = "rgb(251, 135, 66)";
$linklist = $links->load_linklist_by_userid($USER->id);
$templatedata->links = $linklist;
$coodleuser = new \local_coodle\coodle_user();
$coodleuser->load_user($USER->id);
$templatedata->adresscard = \local_coodle\advisor::get_advisor_addrescard($USER->id);

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_coodle/mobile_select_user', ['users' => $users]);

echo $OUTPUT->footer();

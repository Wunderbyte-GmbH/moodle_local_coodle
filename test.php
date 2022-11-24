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
 * Testfile for developing
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('../../course/lib.php');

use local_coodle\form\user_create_form;
use local_coodle\local\views\secondary;

$delid = optional_param('del', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

$PAGE->set_url(new moodle_url('/local/coodle/index.php', array()));

$title = "cOOdle Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);
global $DB;
$data = new \stdClass();
require_once($CFG->dirroot . '/user/lib.php');

$a = \user_get_users_by_id([4]);

$b = "reta";


// Workflow to create user after form.
// TODO: Dynamic Form.
// TODO: Function to create user.
/* global $CFG;
require_once($CFG->dirroot.'/user/lib.php');
$user = new stdClass();
$user->username = "1234sdadasd";
$user->firstname = "ASdasd";
$user->lastname = "asdasdasd";
$user->email = 'blob5@example.com';
$user->password = 'A12!adasd';
$user->confirmed = true;
$user->mnethostid = $CFG->mnet_localhost_id;
$clientid = user_create_user($user, true, false);

\local_coodle\advisor::course_manual_enrolments(array(3), array($guestuserid), 5); */
$mform = new user_create_form();

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

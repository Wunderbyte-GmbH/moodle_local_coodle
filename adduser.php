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
use local_coodle\local\views\secondary;
use local_coodle\form\user_create_form;
use local_coodle\coodle_user;
use local_coodle\form\user_create_form_helper;

$delid = optional_param('userid', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

//if is my user or i am admin?

$secondarynav = new secondary($PAGE);
$secondarynav->initialise();
$PAGE->set_secondarynav($secondarynav);
$PAGE->set_secondary_navigation(true);

$PAGE->set_url(new moodle_url('/local/coodle/myuser.php', array()));
$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);

$mform = new user_create_form(null, null, 'post', '', [], true, []);
$mform->add_action_buttons();

if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if (!empty($fromform = $mform->get_data())) {
    $userid = user_create_form_helper::create_user($fromform);
    // TODO: ?.
    \coodle_user::create_coodle_user($userid, $fromform->userid);
    //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
    $mform->render();
}

echo $OUTPUT->header();

echo $OUTPUT->footer();

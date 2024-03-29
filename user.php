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
 * Show one user of coodle.
 *
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
use local_coodle\local\views\secondary;
use local_coodle\coodle_user;
use local_coodle\permission;

$id = required_param('id', PARAM_INT);
/*
if (!iscoodleclient_from_manager($id)) {
    exit();
} */
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

permission::require_is_advisor_from_user($id);
$secondarynav = new secondary($PAGE);
$secondarynav->initialise();
$PAGE->set_secondarynav($secondarynav);
$PAGE->set_secondary_navigation(true);

$PAGE->set_url(new moodle_url('/local/coodle/user.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
// Get User Object.
$client = new stdClass();
$user = get_complete_user_data('id', $id);
$client->name = fullname($user);
$title = $client->name;
$PAGE->set_title("COODLE");

echo $OUTPUT->header();

$todo = new \local_coodle\todo();
$templatedata = new stdClass();
$todolist = $todo->load_todolist_by_userid($id);
if (!empty($todolist)) {
    $templatedata->todos = $todolist;
} else {
    $templatedata->empty = 1;
}

$todolistadv = $todo->load_todolist_for_advisor($id);
if (!empty($todolistadv)) {
    $templatedata->todos2 = $todolistadv;
} else {
    $templatedata->empty = 1;
}

$templatedata->name = $client->name;
$templatedata->username = $user->username;

$convid = local_coodle\coodle_user::get_conversation_between_users($USER->id, $id);
if ($convid) {
    $templatedata->widget = \core_message\helper::render_messaging_widget(false, $id, $convid);
}
$templatedata->clientid = $id;
$coodle = new local_coodle\coodle_user();
$coodle->load_user($id);
// Load different templatedata.
$templatedata->userid = $id;
$templatedata->qrcode = $coodle->prepare_qr_code_for_template($coodle);
$templatedata->password = $coodle->prepare_qr_code_for_template($coodle);

$templatedata->files[1] = $coodle->get_coodleuser_files(1);
$templatedata->files[2] = $coodle->get_coodleuser_files(2);
$templatedata->files[3] = $coodle->get_coodleuser_files(3);
$templatedata->files[4] = $coodle->get_coodleuser_userfiles($id);

$templatedata->directions = $coodle->get_coodleuser_directions($id);
$templatedata->links = array_values($coodle->get_coodleuser_links($id));
$events = \local_coodle\external\get_calendar_events::execute($id);
$templatedata->events = [];
foreach ($events['events'] as $event) {
    $templatedata->events[] = [
        'name' => $event->name,
        'timestart' => date("d.m H:i", $event->timestart),
        'location' => $event->location,
        'description' => $event->description,
        'id' => $event->id,
    ];
}

echo $OUTPUT->render_from_template('local_coodle/myuser', $templatedata);

echo $OUTPUT->footer();

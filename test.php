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
$id = required_param('userid', PARAM_INT);

$context = \context_system::instance();
$PAGE->set_context($context);


require_login();

// TODO delete before production

$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

// $user = ;
$message = new \core\message\message();
$message->component = 'local_coodle'; // Your plugin's name
$message->name = 'testmessage'; // Your notification name from message.php
$message->userfrom = core_user::get_noreply_user(); // If the message is 'from' a specific user you can set them here
$message->userto = $user;
$message->subject = 'message subject 1';
$message->fullmessage = 'message body';
$message->fullmessageformat = FORMAT_MARKDOWN;
$message->fullmessagehtml = '<p>message body</p>';
$message->smallmessage = 'small message';
$message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
$message->contexturl = (new \moodle_url('/course/'))->out(false); // A relevant URL for the notification
$message->contexturlname = 'Course list'; // Link title explaining where users get to for the contexturl
$content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
$message->set_additional_content('email', $content);

// You probably don't need attachments but if you do, here is how to add one
$usercontext = context_user::instance($USER->id);

$message->attachment = $file;

// Actually send the message
$messageid = message_send($message);


// File to test function till production TODO: Delete file before release.

echo $OUTPUT->footer();

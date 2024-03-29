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

namespace local_coodle;

use coding_exception;
use dml_exception;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class message.
 *
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coodle_pushnotification {

    /**
     * Userid of user to send
     *
     * @var int
     */
    public int $userid;

    /**
     * userid of advisor (userfrom)
     *
     * @var stdClass
     */
    public \stdClass $userfrom;

    /**
     * The message object
     *
     * @var [type]
     */
    public $message;

    /**
     * Constructor for coodle_pushnotification
     * Handles all messages send to coodle app
     *
     * @param integer $userid
     */
    public function __construct(int $userid) {
        global $USER;
        $this->userfrom = $USER;
        $this->userid = $userid;
        $message = new \core\message\message();
        $message->component = 'local_coodle';
        $message->userfrom = $this->userfrom; // If the message is 'from' a specific user you can set them here
        $message->userto = \core_user::get_user($this->userid);
        $message->notification = 1;
        $this->message = $message;
    }

    /**
     * Send a todomsg
     *
     * @param mixed $todo
     * @return void
     */
    public function send_todo_message($todo) {
        $this->message->name = 'newtodomsg';
        $this->message->subject = get_string('newtodo', 'local_coodle');
        $this->message->fullmessage = get_string('newtodo:full', 'local_coodle', $todo->text);
        $this->message->fullmessageformat = FORMAT_MARKDOWN;
        $this->message->fullmessagehtml = '<p>' . get_string('newtodo:full', 'local_coodle', $todo->text)
        . '</p>';
        $this->message->smallmessage = get_string('newtodo:small');

        $customdata = new stdClass();
        $customdata->coodle = true;
        $customdata->coodleurl = 'view_todos/0';
        $customdata->title = 'addon.local_coodle.viewtodos';

        $this->message->customdata = json_encode($customdata);
        $messageid = message_send($this->message);
        return $messageid;
    }

    /**
     * Send a msg when a new file is sent
     *
     * @param mixed $file
     * @return mixed
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function send_newfile_message($file) {
        $this->message->name = 'newfilemsg';
        $this->message->subject = get_string('newfile', 'local_coodle', $file->name);
        $this->message->fullmessage = get_string('newfilewasadded:full', 'local_coodle', $file->name);
        $this->message->fullmessageformat = FORMAT_MARKDOWN;
        $this->message->fullmessagehtml = '<p>' . get_string('newfilewasadded:small', 'local_coodle', $file->name)      . '</p>';
        $this->message->smallmessage = get_string('newfilewasadded:small', 'local_coodle', $file->name);
        $customdata = new stdClass();
        $customdata->coodle = true;
        $customdata->coodleurl = 'view_files3/0';
        $customdata->title = 'addon.local_coodle.dok3';

        $this->message->customdata = json_encode($customdata);

        $messageid = message_send($this->message);
        return $messageid;
    }
}

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

use stdClass;

/**
 * Class coodle user.
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coodle_user {

    private $token;

    private $id;

    private $userid;

    public function __construct() {

    }

    /**
     * Load user
     *
     * @param integer $userid
     * @return stdClass $coodleuser
     */
    public function load_user(int $userid) {
        global $DB;
        $coodleuser = $DB->get_record('local_coodle_user', array('userid' => $userid));
        $this->token = $coodleuser->token;
        $this->id = $coodleuser->id;
        $this->userid = $coodleuser->userid;

        return $coodleuser;
    }
    /**
     * Creates a course with the name of the advisor
     *
     * @param int $userid
     * @param int $advisorid
     * @return int
     */
    public static function create_coodle_user(int $userid, int $advisorid = null) {
        global $DB;
        $data = new stdClass();
        $data->userid = $userid;
        $data->advisorid = $advisorid;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->deleted = 0;
        $data->token = self::generate_coodle_token();

        $coodleuserid = $DB->insert_record('local_coodle_user', $data, true);
        if (!empty($advisorid)) {
            $advisorcourseid = \local_coodle\advisor::get_advisor_course($advisorid);

            // Enrol user in course.
            \local_coodle\advisor::course_manual_enrolments(array($advisorcourseid), array($coodleuserid), 5);

            // Add user to contacts.
            \core_message\api::create_contact_request($advisorid, $userid);
            \core_message\api::confirm_contact_request($advisorid, $userid);
            if (!$conversationid = \core_message\api::get_conversation_between_users([$advisorid, $userid])) {
                // It's a private conversation between users.
                $conversation = \core_message\api::create_conversation(
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                    [
                    $advisorid,
                    $userid
                    ]
                );
            }
            // We either have found a conversation, or created one.
            $conversationid = !empty($conversationid) ? $conversationid : $conversation->id;
            $welcomemsg = "Herzlich Willkommen in der coodle App"; //TODO crete string
            \core_message\api::send_message_to_conversation($advisorid, $conversationid, $welcomemsg, FORMAT_HTML);

            // Create a group between user and advisor.
            \local_coodle\advisor::create_group_for_advisor($advisorid, $userid);
        }
        return $coodleuserid;
    }

    private static function generate_coodle_token() {
        $token = bin2hex(random_bytes(32));
        return $token;
    }

    public function renew_token() {
        global $DB;
        $params = array(
            'tokencreated' => time(),
            'token' => self::generate_coodle_token(),
            'id' => $this->id
        );
        $DB->update_record('local_coodle_user', $params);
    }

    public function compare_token($token) {
        if ($this->token == $token) {
            return true;
        }
        return false;
    }

    public static function get_conversation_between_users($advisorid, $userid) {
        if (!$conversationid = \core_message\api::get_conversation_between_users([$advisorid, $userid])) {
            // It's a private conversation between users.
            $conversation = \core_message\api::create_conversation(
                \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                [
                $advisorid,
                $userid
                ]
            );
        }
        // We either have found a conversation, or created one.
        $conversationid = !empty($conversationid) ? $conversationid : $conversation->id;
        return $conversationid;
    }

    /**
     * Gets all coodle users (clients) with user data from MOODLE user table
     *
     * @return array
     */
    public static function get_all_coodle_users() {
        global $DB;
        $sql = "SELECT cu.*, u.firstname as 'clientfirstname', u.lastname as 'clientlastname',
        ua.firstname as 'advisorfirstname', ua.lastname as 'advisorlastname', a.courseid
         FROM {local_coodle_advisor} a RIGHT JOIN {local_coodle_user} cu on a.userid = cu.advisorid
         JOIN {user} u on cu.userid = u.id
         LEFT JOIN {user} ua on cu.advisorid = ua.id";
        $data = $DB->get_records_sql($sql);
        return $data;
    }

    // TODO: user to advisor
    public static function get_coodle_users($userid) {
        global $DB, $USER;

        if(!$userid) {
            $userid = $USER->id;
        }
        $sql = "SELECT cu.*, u.firstname as 'clientfirstname', u.lastname as 'clientlastname',
        ua.firstname as 'advisorfirstname', ua.lastname as 'advisorlastname', a.courseid
         FROM {local_coodle_advisor} a RIGHT JOIN {local_coodle_user} cu on a.userid = cu.advisorid
         JOIN {user} u on cu.userid = u.id
         LEFT JOIN {user} ua on cu.advisorid = ua.id
         WHERE cu.advisorid = $userid";
        $data = $DB->get_records_sql($sql);
        return $data;
    }

    public static function prepare_coodle_users_for_select(array $users) {
        $select = [];
        foreach ($users as $user) {
            $id = $user->userid;
            $select[$id] = $user->clientfirstname . ' ' . $user->clientlastname;
        }
        return $select;
    }

    public static function get_coodle_todos($userid) {
        global $DB;
        $data = $DB->get_records('local_coodle_todos', array('userid' => $userid));
        return $data;
    }
    /**
     * Prepares date for mustache template
     *
     * @return array
     */
    public static function prepare_for_template($userid = 0): array {
        if (!$userid) {
            $coodleusers = self::get_all_coodle_users();
        } else {
            $coodleusers = self::get_coodle_users($userid);
        }

        $templatedata = [];
        foreach ($coodleusers as $coodleuser) {
            $tdata = $coodleuser;
            $tdata->userdatecreated = date("Y-m-d", $coodleuser->timecreated);
            $qrcodeimg = \local_coodle\overrides\mobileapioverrides::generate_login_qrcode_from_userid($tdata->token, $coodleuser->userid);
            $mobileqr = \html_writer::link('#qrcode-'.$coodleuser->userid, '',
                ['class' => 'btn btn-primary mt-2 fa fa-qrcode', 'data-toggle' => 'collapse',
                'role' => 'button', 'aria-expanded' => 'false']);
            $mobileqr .= \html_writer::div(\html_writer::img($qrcodeimg, 'token', ['class' => 'qrcode']), 'collapse mt-4', ['id' => 'qrcode-'.$coodleuser->userid]);
            $tdata->qrcode = $mobileqr;
            $tdata->todos = array_values(self::get_coodle_todos($coodleuser->userid));
            $templatedata[] = $tdata;
        }
        return $templatedata;
    }

    public function get_coodleuser_links(): string {
        return "<ul class='list-group'><li class='list-group-item'><a href='/local/coodle/advisoradress.php'>Lebenslauf schreiben leicht gemacht!</a></li></ul>";
    }

    public function get_coodleuser_directions(): string {
        return "<ul class='list-group'><li class='list-group-item'><a href='/local/coodle/advisoradress.php'>Mein Büro</a></li></ul>";
    }

    public function get_coodleuser_userfiles($userid): array {
        $context = \context_user::instance($userid);

        // Get the file storage instance
        $filestorage = get_file_storage();

        // Get all files from the file storage
        $files = $filestorage->get_directory_files($context->id, 'local_coodle', 'clientfile', 0, '/');
        $fileoutput = array();
        // Output the file information
        foreach ($files as $file) {
            if ($file->get_filename() != '.') {
                $fileinfo = new stdClass();
                $fileinfo->id = $file->get_id();
                $fileinfo->name = $file->get_filename();
                $fileinfo->filesize = $file->get_filesize();
                $fileinfo->filesize = $file->get_mimetype();
                $fileinfo->timemodified = time(); // TODO:;
                $fileinfo->url = \moodle_url::make_pluginfile_url(
                    $context->id,
                    'local_coodle',
                    'clientfile',
                    0,
                    '/',
                    $file->get_filename(),
                    false
                );
                $fileoutput[] = $fileinfo;
            }
        }

        return $fileoutput;
    }


    /**
     * Get files from different areas
     *
     * @param integer $doctype
     * @return array
     */
    public function get_coodleuser_files(int $doctype): array {
        global $USER;
        $context = \context_system::instance();

        // Get the file storage instance
        $filestorage = get_file_storage();

        // Get all files from the file storage
        $files = $filestorage->get_directory_files($context->id, 'local_coodle', 'clientfiles', 0, '/' . $this->userid . '/' . $doctype . '/');
        $fileoutput = array();
        // Output the file information
        foreach ($files as $file) {
            if ($file->get_filename() != '.') {
                $fileinfo = new stdClass();
                $fileinfo->id = $file->get_id();
                $fileinfo->name = $file->get_filename();
                $fileinfo->namewithoutextension = pathinfo($fileinfo->name, PATHINFO_FILENAME);
                $fileinfo->filesize = $file->get_filesize();
                $fileinfo->filesize = $file->get_mimetype();
                $fileinfo->timemodified = time(); // TODO:;
                $fileinfo->userid = $file->get_userid();
                if ($fileinfo->userid == $USER->id) {
                    $fileinfo->deleteable = true;
                }
                $fileinfo->url = \moodle_url::make_pluginfile_url(
                    $context->id,
                    'local_coodle',
                    'clientfiles',
                    0,
                    '/' .$this->userid . '/' . $doctype . '/',
                    $file->get_filename(),
                    false
                );
                // TODO: check if user or advisorfile
                $fileoutput[] = $fileinfo;
            }
        }

        return $fileoutput;
    }


    public static function delete_file($fileid) {
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($fileid);
        if ($file) {
            $file->delete();
        }
    }
    /**
     * Counts all clients in coodle DB
     *
     * @return int
     */
    public static function count_users() {
        global $DB;
        return $DB->count_records('local_coodle_user', null);
    }



    /**
     * Delete one User (sets flag in DB not deleted from whole Platform)
     *
     * @return int
     */
    public static function delete_user($clientid) {
        global $DB;
        $params = array(
            'timemodified' => time(),
            'userid' => $clientid,
            'deleted' => 1
        );
        return $DB->update_record('local_coodle_user', $params);
    }
}

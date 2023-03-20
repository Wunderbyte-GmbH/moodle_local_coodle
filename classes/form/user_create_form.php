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
 * Entitiesrelation form implemantion to use entities in other plugins
 * @package     local_coodle
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coodle\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

use context;
use core_form\dynamic_form;
use local_coodle\form\user_create_form_helper;
use local_coodle\coodle_user;
use moodle_url;
use stdClass;
/**
 * Create a user and add it to advisor course.
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_create_form extends dynamic_form {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $mform = $this->_form;

        $data = $this->_ajaxformdata;
        /*
        if (has_capability())

        else if is_advisor

        else (die());
        */

        $mform->addElement('text', 'username', get_string('username'), 'size="20"');
        $mform->addHelpButton('username', 'username', 'auth');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', null, 'required', null, 'server');

        $mform->addElement('text', 'firstname', get_string('firstname'), 'size="20"');
        $mform->setType('firstname', PARAM_RAW);
        $mform->addRule('firstname', null, 'required', null, 'server');

        $mform->addElement('text', 'lastname', get_string('lastname'), 'size="20"');
        $mform->setType('lastname', PARAM_RAW);
        $mform->addRule('lastname', null, 'required', null, 'server');


        $mform->addElement('text', 'email', get_string('email'), 'size="20"');
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', null, 'required', null, 'server');

        if (!\local_coodle\advisor::is_advisor()) {
            //TODO add select
            $userlist = \local_coodle\advisor::get_advisor_list();
        } else {
            global $USER;
            $mform->addElement('hidden', 'advisorid', $USER->id);
        }
        $mform->addElement('passwordunmask', 'newpassword', get_string('newpassword'), 'size="20"');
        $mform->addHelpButton('newpassword', 'newpassword');
        $mform->setType('newpassword', \core_user::get_property_type('password'));

        // Shared fields.
        // Next the customisable profile fields.
        // profile_definition($mform, 0);
    }

    /**
     * Check access for dynamic submission.
     *
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/user:manageownfiles', $this->get_context_for_dynamic_submission());
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * @return mixed
     */
    public function process_dynamic_submission() {
        $data = $this->get_data();
        $userid = user_create_form_helper::create_user($data);
        coodle_user::create_coodle_user($userid, $data->advisorid);
        return $data;
    }


    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $this->set_data(get_entity($this->_ajaxformdata['cmid']));
     */
    public function set_data_for_dynamic_submission(): void {
        $data = new stdClass();
        $this->set_data($data);
    }

    /**
     * Returns form context
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        global $USER;
        return \context_user::instance($USER->id);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $cmid = $this->_ajaxformdata['cmid'];
        if (!$cmid) {
            $cmid = $this->optional_param('cmid', '', PARAM_RAW);
        }
        return new moodle_url('/local/coodle/newuser', array('id' => $cmid));
    }

    /**
     * {@inheritdoc}
     * @see moodleform::validation()
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     */
    public function validation($data, $files) {

        $errors = array();

        return $errors;
    }

    /**
     * {@inheritDoc}
     * @see moodleform::get_data()
     */
    public function get_data() {
        $data = parent::get_data();
        return $data;
    }

    // Helperfunctio
}

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
use context_course;
use core_form\dynamic_form;
use moodle_url;
use stdClass;
use calendar_event;
/**
 * Add file form.
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_calendar_form extends dynamic_form {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     */
    public function definition() {
        global $USER, $DB;
        $mform = $this->_form;
        $data = $this->_ajaxformdata;

        // get advisor courseid
        // get ad

        // Add some hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setDefault('userid', $data['clientid']);

        $mform->addElement('hidden', 'modulename');
        $mform->setType('modulename', PARAM_INT);
        $mform->setDefault('modulename', '');

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', 0);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_INT);

        // Normal fields
        $mform->addElement('text', 'name', get_string('eventname','calendar'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('editor', 'description', get_string('eventdescription','calendar'), null);
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('date_time_selector', 'timestart', get_string('date'));
        $mform->addRule('timestart', get_string('required'), 'required');

        $mform->addElement('header', 'durationdetails', get_string('eventduration', 'calendar'));

        $group = array();
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationnone', 'calendar'), 0);
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationuntil', 'calendar'), 1);
        $group[] =& $mform->createElement('date_time_selector', 'timedurationuntil', '');
        $group[] =& $mform->createElement('radio', 'duration', null, get_string('durationminutes', 'calendar'), 2);
        $group[] =& $mform->createElement('text', 'timedurationminutes', get_string('durationminutes', 'calendar'));

        $mform->addGroup($group, 'durationgroup', '', '<br />', false);

        $mform->disabledIf('timedurationuntil',         'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[day]',    'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[month]',  'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[year]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[hour]',   'duration', 'noteq', 1);
        $mform->disabledIf('timedurationuntil[minute]', 'duration', 'noteq', 1);

        $mform->setType('timedurationminutes', PARAM_INT);
        $mform->disabledIf('timedurationminutes','duration','noteq', 2);

    }

    /**
     * Check access for dynamic submission.
     *
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        // TODO: capability to create advisors
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
        global $CFG, $DB, $USER;
        $data = $this->get_data();
        $user = \core_user::get_user($data->userid);
        $courseid = \local_coodle\advisor::get_advisor_course($USER->id);
        $groupname = fullname($user) . ' (' . $data->userid . ')';
        $group = $DB->get_record('groups', array('courseid' => $courseid, 'name' => $groupname));
        require_once($CFG->dirroot.'/calendar/lib.php');
        if ($group && $courseid && $user) {
            $event = new stdClass();
            $event->eventtype = "group";
            $event->name = $data->name;
            $event->description = format_text($data->description, FORMAT_MOODLE);
            $event->modulename = "0";
            $event->component = null;
            $event->format = FORMAT_HTML;
            $event->courseid = $courseid;
            $event->groupid = $group->id;
            $event->userid = $USER->id;
            $event->instance = 0;
            $event->visible = 1;
            $event->timestart = $data->timestart;
            $event->timeduration = 0;
            $event->timedurationuntil = $data->timedurationuntil;
            $event->eventrepeats = 0;
            $event->duration = "0";
            $event->location = $data->location;

            calendar_event::create($event);
            return json_encode(array("error" => false));
        }

        return json_encode(array("error" => true));
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
        // TODO: This is shit.
        $cmid = $this->_ajaxformdata['clientid'];
        if (!$cmid) {
            $cmid = $this->optional_param('cmid', '', PARAM_RAW);
        }
        return new moodle_url('/local/coodle/user', array('id' => $cmid));
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
}

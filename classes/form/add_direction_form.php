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
use moodle_url;
use context_system;
use local_coodle\direction;
use stdClass;
/**
 * Add file form.
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_direction_form extends dynamic_form {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $mform = $this->_form;
        $data = $this->_ajaxformdata;
        $mform->addElement('text', 'title', get_string('description', 'local_coodle'));

        $context = context_system::instance();

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES,
        'noclean' => true,
        'context' => $context,
        'format' => FORMAT_HTML);

        $mform->addElement('editor', 'description', get_string('description', 'local_coodle'), '', $editoroptions);
        $mform->addElement('hidden', 'userid', $data['clientid']);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'id', $data['directionid']);
        $mform->setType('id', PARAM_INT);
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
        $data = $this->get_data();
        $context = context_system::instance();
        $draftitemid = $data->description['itemid'];
        $data->description = $data->description['text'] ?? $data->description ?? '';
        $data->text = $data->description;

        $adress = new \local_coodle\direction($data);
        $adressid = $data->id;
        if (!empty($adressid)) {
            $adress->update_direction($data);
        } else {
            $adressid = $adress->add_direction();
        }
        if (isset($draftitemid)) {
            $data->description = file_save_draft_area_files($draftitemid, $context->id,
            'local_coodle', 'direction',
            $adressid, array('subdirs' => true), $data->description);
            $updatedate = new stdClass();
            $updatedate->id = $adressid;
            $updatedate->text = $data->description;
            $adress->update_direction($updatedate);
        }

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
        $data = $this->_ajaxformdata;
        if (isset($data['directionid']) && is_numeric($data['directionid']) && $data['directionid'] > 0) {
            $direction = new direction(null, $data['directionid']);
            $data['title'] = $direction->direction->title;
            $data['description']['text'] = $direction->direction->text;
            $data['description']['format'] = FORMAT_HTML;
        }
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

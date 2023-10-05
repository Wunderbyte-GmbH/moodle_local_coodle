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
use stdClass;
/**
 * Add file form.
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_file_form extends dynamic_form {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $mform = $this->_form;
        $customdata = $this->_ajaxformdata;

        $id = optional_param('userid', 0, PARAM_INT);

        // IMAGE CONTENT.
        $options['subdirs'] = 0;
        $options['maxbytes'] = 204800;
        $options['maxfiles'] = null;
        $options['accepted_types'] = ['jpg', 'jpeg', 'png', 'pdf' , 'doc', 'xls', 'docx'];
        $mform->addElement('filemanager', 'clientfiles_filemanager', get_string('uploadfile', 'local_coodle'), null, $options);
        $mform->addElement('hidden', 'id', $customdata['clientid']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'doctype', $customdata['doctype']);
        $mform->setType('doctype', PARAM_INT);
        $mform->addElement('hidden', 'sendmsg', $customdata['sendmsg']);
        $mform->setType('sendmsg', PARAM_BOOL);

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
        global $USER;

        $data = $this->get_data();
        $context = \context_system::instance();

        $options = array('subdirs' => 0, 'maxbytes' => 204800, 'maxfiles' => 10, 'accepted_types' => array('jpg', 'png', 'jpeg'));
        if (isset($data->clientfiles_filemanager)) {
            file_postupdate_standard_filemanager($data, 'clientfiles', $options, $context, 'local_coodle', 'clientfiles', $data->id);
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'local_coodle', 'clientfiles', $data->id);
            foreach ($files as $file) {
                if ($file->get_filename() != '.') {
                    $filerecord = [
                        'contextid'    => $file->get_contextid(),
                        'component'    => $file->get_component(),
                        'filearea'     => 'clientfiles',
                        'itemid'       => 0,
                        'filepath'     => '/'.$data->id.'/'.$data->doctype.'/',
                        'filename'     => $file->get_filename(),
                        'timecreated'  => time(),
                        'timemodified' => time(),
                    ];
                    $newfile = $fs->create_file_from_storedfile($filerecord, $file);
                    // Send a push notification
                    $message = new \local_coodle\coodle_pushnotification($data->id);
                    $message->send_newfile_message($file);

                    if ($data->sendmsg) {
                        $url = moodle_url::make_pluginfile_url(
                            $newfile->get_contextid(), $newfile->get_component(),
                            $newfile->get_filearea(), $newfile->get_itemid(), $newfile->get_filepath(), $newfile->get_filename(), false);
                        $path = pathinfo($url);
                        switch ($path['extension']) {
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                            case 'gif':
                                // Handle image extensions
                                $msg = "<img src='$url'>";
                                break;

                            case 'mp4':
                            case 'avi':
                            case 'mkv':
                                // Handle video extensions
                                $msg = "This is a video." . $path['filename'];
                                break;

                            default:
                                // Handle other extensions
                                $msg = "<a href='$url' target='_blank'>" . $path['filename'] . "</a>";
                                break;
                        }
                        $conversationid = \core_message\api::get_conversation_between_users([$USER->id,  $data->clientid]);
                         \core_message\api::send_message_to_conversation($USER->id, $conversationid, $msg, FORMAT_HTML);

                    }
                    // Now delete the original file.
                    $file->delete();
                }
            }

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
        $data['id'] = $this->_ajaxformdata['clientid'];
        $data['doctype'] = $this->_ajaxformdata['doctype'];

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

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

namespace local_coodle\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

use context;
use context_system;
use core_form\dynamic_form;
use local_coodle\permission;
use moodle_url;
use stdClass;

/**
 * Form to add an entry.
 *
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_password extends dynamic_form {

    /**
     * Get context for dynamic submission.
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return context_system::instance();
    }

    /**
     * Check access for dynamic submission.
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        $data = $this->_ajaxformdata;
        permission::require_is_advisor_from_user($data['id']);
    }

    /**
     * Set data for dynamic submission.
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
    }
    /**
     * Process dynamic submission.
     * @return stdClass|null
     */
    public function process_dynamic_submission(): stdClass {
        global $CFG;
        $data = $this->get_data();
        $user = new stdClass();
        $user->id = $data->id;
        $user->password = $data->password;
        permission::require_is_advisor_from_user($user->id);
        require_once($CFG->dirroot.'/user/lib.php');

        \user_update_user($user, true, false);
        return $data;
    }

    /**
     * Form definition.
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        $customdata = $this->_ajaxformdata;

        $mform->addElement('hidden', 'id',  $customdata['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('text', 'password', get_string('newpassword'));

    }

    /**
     * Server-side form validation.
     * @param array $data
     * @param array $files
     * @return array $errors
     */
    public function validation($data, $files): array {
        $errors = [];
        check_password_policy($data['password'], $errmsg);
        if (!empty($errmsg)) {
            $errors['password'] = $errmsg;
        }
        return $errors;
    }

    /**
     * Get page URL for dynamic submission.
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/coodle/admin.php');
    }
}

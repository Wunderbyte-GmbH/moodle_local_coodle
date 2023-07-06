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
 * Class for exporting a course module summary from an stdClass.
 *
 * @package    local_rk_manager
 * @copyright  2022 Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_coodle\external;

use external_api;
use external_single_structure;
use moodle_exception;
use external_function_parameters;
use external_value;

class get_sesskey extends external_api {

    /**
     * Delete an entry from the local_rk_manager_entry table.
     *
     * @param int $entryid The ID of the entry to delete
     * @return external_function_parameters
     * @throws moodle_exception if the entry is not found or the user is not authorized to delete the entry
     */
    public static function execute_parameters() : external_function_parameters {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'The ID of the entry to delete'),
        ));
    }

    public static function execute($userid) {
        global $USER, $DB;

        $params = self::validate_parameters(self::execute_parameters(), array('entryid' => $userid));
        $userid = $params['userid'];

        $entry = $DB->get_record('local_rk_manager_entry', array('id' => $entryid));
        if (!$entry) {
            throw new moodle_exception('entrynotfound', 'local_rk_manager');
        }

        if ($entry->userid != $USER->id) {
            throw new moodle_exception('notauthorized', 'local_rk_manager');
        }

        $DB->delete_records('local_rk_manager_entry', array('id' => $entryid));

        return array($sesskey = "asdasd");
    }

    public static function execute_returns() {
        return new external_single_structure(
            'sesskey' => new \external_value(PARAM_TEXT, 'sesskey')
        );
    }
}

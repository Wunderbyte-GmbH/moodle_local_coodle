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
use external_multiple_structure;
use moodle_exception;
use external_function_parameters;
use external_value;
use external_util;

class get_files extends external_api {

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

        $context = \context_user::instance($USER->id);

        $testfiles['files'] = external_util::get_area_files($context->id, 'local_coodle', 'clientfile', false, false);
        return $testfiles;
    }


    public static function execute_returns() {
        return new external_single_structure(
            array(
                'files' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'contextid' => new external_value(PARAM_INT, ''),
                            'component' => new external_value(PARAM_COMPONENT, ''),
                            'filearea'  => new external_value(PARAM_AREA, ''),
                            'itemid'   => new external_value(PARAM_INT, ''),
                            'filepath' => new external_value(PARAM_TEXT, ''),
                            'filename' => new external_value(PARAM_TEXT, ''),
                            'isdir'    => new external_value(PARAM_BOOL, ''),
                            'url'      => new external_value(PARAM_TEXT, ''),
                            'timemodified' => new external_value(PARAM_INT, ''),
                            'timecreated' => new external_value(PARAM_INT, 'Time created', VALUE_OPTIONAL),
                            'filesize' => new external_value(PARAM_INT, 'File size', VALUE_OPTIONAL),
                            'author' => new external_value(PARAM_TEXT, 'File owner', VALUE_OPTIONAL),
                            'license' => new external_value(PARAM_TEXT, 'File license', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }
}

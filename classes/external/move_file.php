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
 * @package    local_coodle
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
use stdClass;

class get_files extends external_api {

    /**
     * Get all the files from coodle user clientfile area.
     *
     * @param int $entryid The ID of the entry to delete
     * @return external_function_parameters
     * @throws moodle_exception if the entry is not found or the user is not authorized to delete the entry
     */
    public static function execute_parameters() : external_function_parameters {
        return new external_function_parameters([
            // 'userid' => new external_value(PARAM_INT, 'The ID of the entry to delete'),
        ]);
    }

    /**
     * Execute the function
     *
     * @return array
     */
    public static function execute() {
        global $USER, $DB;

        $context = \context_user::instance($USER->id);

        // Get the file storage instance
        $filestorage = get_file_storage();

        // Get all files from the file storage
        $files = $filestorage->get_directory_files($context->id, 'local_coodle', 'clientfile', 0, '/' );

        // Output the file information
        foreach ($files as $file) {
            if ($file->get_filename() != '.') {

                $fileinfo = [
                    'contextid' => $context->id,
                    'component' => 'local_coodle',
                    'filearea'  => 'clientfile',
                    'itemid'   => $file->get_id(),
                    'filepath' => '/',
                    'filename' => $file->get_filename(),
                    'isdir'    => false,
                    'url'      => \moodle_url::make_pluginfile_url(
                        $context->id,
                        'local_coodle',
                        'clientfile',
                        0,
                        '/' .  $file->get_filename(),
                        false
                    )->out(),
                    'timemodified' => time(),
                ];
                $fileoutput[] = $fileinfo;
            }
        }

        $testfiles['files'] = $fileoutput;
        // TODO: get parents lol.
        $parents = [];
        $testfiles['parents'] = $parents;
        // $testfiles['files'] = external_util::get_area_files($context->id, 'local_coodle', 'clientfile', false, false);
        return $testfiles;
    }

    /*
     * Returns files of coodle userfilearea.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                'parents' => new external_single_structure(
                    []
                ),
                'files' => new external_multiple_structure(
                    new external_single_structure(
                        [
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
                        ]
                    )
                ),
            ]
        );
    }
}

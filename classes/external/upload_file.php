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
 * This class contains a list of webservice functions related to the Shopping Cart Module by Wunderbyte.
 *
 * @package    local_coodle
 * @copyright  2022 Thomas Winkler <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_coodle\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class upload_file extends external_api {



    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            array(
                'draftid' => new external_value(PARAM_INT, 'draft area id'),
                'filename' => new external_value(PARAM_TEXT, 'text')
            )
        );
    }
    /**
     * Copy files from a draft area to coodle files area.
     *
     * @throws invalid_parameter_exception
     * @param int $draftid Id of a draft area containing files.
     * @param string $filename of the uploaded file.
     */
    public static function execute($draftid, $filename) {
        global $CFG, $USER;
        require_once($CFG->libdir . "/filelib.php");

        $params = self::validate_parameters(self::execute_parameters(), array('draftid' => $draftid, 'filename' => $filename));

        if (isguestuser()) {
            throw new \invalid_parameter_exception('Guest users cannot upload files');
        }

        $context = \context_user::instance($USER->id);
        require_capability('moodle/user:manageownfiles', $context);

        $maxbytes = $CFG->userquota;
        $maxareabytes = $CFG->userquota;
        // TODO: real cap.
        if (has_capability('moodle/user:ignoreuserquota', $context)) {
            $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;
            $maxareabytes = FILE_AREA_MAX_BYTES_UNLIMITED;
        }

        $options = array('subdirs' => 1,
                         'maxbytes' => $maxbytes,
                         'maxfiles' => -1,
                         'areamaxbytes' => $maxareabytes);

        file_merge_files_from_draft_area_into_filearea($draftid, $context->id, 'local_coodle', 'clientfile', 0, $options);
        $filestorage = get_file_storage();
        $file = $filestorage->get_file($context->id, 'local_coodle', 'clientfile', 0, '/', $params['filename']);
        $fileurl = \moodle_url::make_pluginfile_url(
            $context->id,
            'local_coodle',
            'clientfile',
            0,
            '/' .  $file->get_filename(),
            false
        )->out();
        return array('fileurl' => $fileurl);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function execute_returns() {
        return new external_single_structure(array(
            'fileurl' => new external_value(PARAM_TEXT, 'fileurl'),
            )
        );
    }
}

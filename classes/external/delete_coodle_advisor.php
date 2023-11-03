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
use local_coodle\permission;
use local_coodle\settings_manager;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class delete_coodle_user extends external_api {

    /**
     * Describes the paramters for add_advisor.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() : external_function_parameters {
        return new external_function_parameters ([
            'userid' => new external_value(PARAM_INT, 'user id', VALUE_REQUIRED),
        ]);
    }
    /**
     * Webservice to create an advisor.
     *
     * @return array
     */
    public static function execute(int $userid): array {
        global $USER;

        // TODO if user can delete coodle user -> delete coodle user;

        $answer['error'] = "";

        permission::require_is_coodleadmin();

        $params = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
        ]);

        $settingsmanager = new settings_manager(1);

        $settingsmanager->delete_advisor($params['userid']);

        $answer['error'] = $settingsmanager->delete_advisor($params['userid']);;

        return $answer;
    }

    /*
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(array(
            'error' => new external_value(PARAM_TEXT, 'error'),
            )
        );
    }
}

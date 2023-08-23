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
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class add_todo extends external_api {

    /**
     * Describes the paramters for add_advisor.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() : external_function_parameters {
        return new external_function_parameters ([
            'userid' => new external_value(PARAM_INT, 'userid', VALUE_REQUIRED),
            'todo' => new external_value(PARAM_TEXT, 'todo', VALUE_REQUIRED),
        ]);
    }
    /**
     * Webservice to create an advisor.
     *
     * @return array
     */
    public static function execute(int $userid, string $todo): array {
        $changetodo['error'] = "";

        $params = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'todo' => $todo
        ]);

        $data = new stdClass();
        $data->clientid = $params['userid'];
        $data->text = $params['todo'];
        $data->usertodo = 1;

        $todo = new \local_coodle\todo($data);
        $todo->add_todo();

        $todoanswer['error'] = '';

        return $todoanswer;
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

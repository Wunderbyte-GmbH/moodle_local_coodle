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
use external_multiple_structure;
use external_warnings;
use context_system;


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class set_coodle_preferences extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
        array(
            'preferences' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                        'value' => new external_value(PARAM_RAW, 'The value of the preference'),
                        'userid' => new external_value(PARAM_INT, 'Id of the user to set the preference'),
                    )
                )
            )
        )
        );
    }

    /**
     * Set user preferences.
     *
     * @param array $preferences list of preferences including name, value and userid
     * @return array of warnings and preferences saved
     * @throws moodle_exception
     */
    public static function execute($preferences) {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), array('preferences' => $preferences));
        $warnings = array();
        $saved = array();

        $context = context_system::instance();
        self::validate_context($context);

        $userscache = array();
        foreach ($params['preferences'] as $pref) {
            // Check to which user set the preference.
            if ($USER->id == $pref['userid']) {
                set_user_preference($pref['name'], $pref['value'], $pref['userid']);
                $saved[] = array(
                    'name' => $pref['name'],
                    'userid' => $pref['userid'],
                );
            }
        }

        $result = array();
        $result['saved'] = $saved;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function execute_returns() {
        return new external_single_structure(
        array(
            'saved' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                        'userid' => new external_value(PARAM_INT, 'The user the preference was set for'),
                    )
                ), 'Preferences saved'
            ),
            'warnings' => new external_warnings()
        )
        );
    }
}

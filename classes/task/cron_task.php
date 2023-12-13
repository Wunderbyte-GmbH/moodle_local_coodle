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
 * Adhoc task handling course module deletion.
 *
 * @package    local_coodle
 * @copyright  2023 Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coodle\task;

use local_coodle\settings_manager;

/**
 * Class handling cohort user deletion.
 *
 * @package local_cohorts
 * @copyright 2023 Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {


    public function get_name() {
        // Return the name of the task.
        return get_string('cron_task', 'local_coodle');
    }

    /**
     * Run the deletion task.
     *
     * @throws \coding_exception if the module could not be removed.
     */
    public function execute() {
        global $CFG, $DB;
        $config = get_config('local_coodle');
        $this->enrol_advisors_to_advisorcourse();
        if ($config->coodledeletetime > 0) {
            $coodledeletetime = $config->coodledeletetime * 3600;
        } else {
            $coodledeletetime = 3600 * 31;
        }

        $records = $DB->get_records('local_coodle_user', ['deleted' => 1]);

        foreach ($records as $record) {
            if ((int)$record->timemodified + $coodledeletetime < time()) {
                \local_coodle\settings_manager::delete_user($record->userid, true);
            }
        }
    }

    public function enrol_advisors_to_advisorcourse() {
        $lastupdate = get_config('local_coodle', 'last_enrolement');
        \local_coodle\advisor::enrol_advisors_to_advisorcourse();
        set_config('last_enrolement', time(), 'local_coodle');
    }
}

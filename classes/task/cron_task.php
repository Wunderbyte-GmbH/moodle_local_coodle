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

namespace local_cohorts\task;

defined('MOODLE_INTERNAL') || die();
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
        return get_string('cron_trask', 'local_coodle');
    }

    /**
     * Run the deletion task.
     *
     * @throws \coding_exception if the module could not be removed.
     */
    public function execute() {
        global $CFG, $DB;
        $config = get_config('local_coodle');

        if ($config->coodledeletetime > 0) {
            $coodledeletetime = $config->coodledeletetime;
        } else {
            $coodledeletetime = 3600 * 31;
        }


        $records = $DB->get_records('local_coodle_users', ['deleted' => 1])

        foreach ($records as $record) {
            if ($record->timemodified + $coodledeletetime < time()) {
                $this->delete()
            }
        }
        // coodle_user delete
        // coodle_files delete
        // coodle_todos delete
        // coodle_
    }

}

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
 * Event observers used in coodle.
 *
 * @package local_coodle
 * @copyright 2023 Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace local_coodle;

use core\event\user_deleted;
use local_coodle\settings_manager;
use local_coodle\advisor;

/**
 * Event observer for local_coodle.
 */
class observers {

    /**
     * Observer for the user_deleted event deletes all newsletter subscriptions of the user
     *
     * @param \core\event\user_deleted $event
     */
    public static function user_deleted(user_deleted $event) {
        global $DB;

        $userid = (int)$event->objectid;
        if ($DB->record_exists('local_coodle_user', ['userid' => $userid])) {
            settings_manager::delete_user($userid, false);
            return;
        }
        if ($DB->record_exists('local_coodle_advisor', ['userid' => $userid])) {
            advisor::delete($userid);
            return;
        }
    }
}

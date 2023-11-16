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

declare(strict_types=1);

namespace local_coodle;

use context;
use context_system;

/**
 * Report permission class
 *
 * @package     local_coodle
 * @copyright   2023 Thomas Winkler
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission {

    /**
     * Require given user can view reports list
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @throws exception
     */
    public static function require_is_coodleadmin(?int $userid = null): void {
        if (!static::is_superadmin($userid)) {
            throw new \exception('not allowed');
        }
    }

    public static function is_superadmin(?int $userid = null): bool {
        global $CFG, $USER, $DB;
        $userid = $userid ?: (int) $USER->id;
        $context = context_system::instance();

        if (is_siteadmin($userid) ||
            has_capability('moodle/coodle:canmanageadvisors', $context, $userid)) {
            return true;
        }
        return false;
    }
}

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

namespace local_coodle\form;

use stdClass;

/**
 * Helper class for user_create_form.
 * @package local_coodle
 * @author Thomas Winkler
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_create_form_helper {

    /**
     * Returns the categoryid in which advisorcourses are listed and created
     *
     * @return int
     */
    public static function create_user(stdClass $data) : int {
        global $CFG;
        require_once($CFG->dirroot.'/user/lib.php');
        $user = $data;
        $user->confirmed = true;
        $user->mnethostid = $CFG->mnet_localhost_id;
        $clientid = user_create_user($user, true, false);
        return $clientid;
    }
}

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
 * Module Wizard external functions and service definitions.
 *
 * @package local_coodle
 * @category external
 * @copyright 2021 Wunderbyte GmbH (info@wunderbyte.at)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
        'local_coodle_add_advisor' => array(
                'classname' => '\local_coodle\external\add_advisor',
                'methodname' => 'execute',
                'description' => 'Add a advisor',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
        ),
        'local_coodle_add_user' => array(
                'classname' => '\local_coodle\external\add_user',
                'methodname' => 'execute',
                'description' => 'Add a coodle user',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
        ),
        'local_coodle_change_todo' => array(
                'classname' => '\local_coodle\external\change_todo',
                'methodname' => 'execute',
                'description' => 'Change todo status to done or delete it',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
        ),
        'local_coodle_get_sesskey' => array(
                'classname' => 'local_rk_manager\external\get_sesskey',
                'classpath' => '',
                'description' => 'Get calendardata from specific entity',
                'type' => 'read',
                'ajax' => true
        ),
);

$services = array(
        'Coodle Tokens' => array( // TODO check if needed?
                'functions' => array (
                        'local_coodle_get_sesskey',
                ),
                'restrictedusers' => 0,
                'shortname' => 'local_coodle_tokens',
                'downloadfiles' => 1,    // Allow file downloads.
                'uploadfiles'  => 1,      // Allow file uploads.
                'enabled' => 1
        ),
);

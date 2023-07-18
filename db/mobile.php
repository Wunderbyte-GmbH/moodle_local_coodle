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
 * Local coodle module mobile definition
 *
 * @package         local_coodle
 * @author          Christian Badusch
 * @copyright       2022 Wunderbyte GmbH
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'local_coodle' => [
        'handlers' => [
            'files1' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_files1',
                'displaydata' => [
                    'title' => 'dok1',
                    'icon' => 'file',
                ],
                'priority'  => 10000000000000000,
                'styles' => array(
                    'url' => $CFG->wwwroot . '/local/coodle/mobile/css/styles_app.css',
                    'version' => '0.2'
                ),
            ],
            'files2' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_files2',
                'displaydata' => [
                    'title' => 'dok2',
                    'icon' => 'file',
                ],
                'priority'  => 10000000000000000,
                'styles' => array(
                    'url' => $CFG->wwwroot . '/local/coodle/mobile/css/styles_app.css',
                    'version' => '0.2'
                ),
            ],
            'files3' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_files3',
                'displaydata' => [
                    'title' => 'dok3',
                    'icon' => 'file',
                ],
                'priority'  => 10000000000000000,
                'styles' => array(
                    'url' => $CFG->wwwroot . '/local/coodle/mobile/css/styles_app.css',
                    'version' => '0.2'
                ),
            ],
            'viewaddress' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_address',
                'displaydata' => [
                    'title' => 'viewaddress',
                    'icon' => 'map',
                ],
                'priority'  => 10000000000000000,
            ],
            'viewtodos' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_todos',
                'displaydata' => [
                    'title' => 'viewtodos',
                    'icon' => 'map',
                ],
                'priority'  => 10000000000000000,
            ],
            'viewinfo' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_info',
                'displaydata' => [
                    'title' => 'viewinfo',
                    'icon' => 'map',
                ],
                'priority'  => 10000000000000000,
                'styles' => array(
                    'url' => $CFG->wwwroot . '/local/coodle/mobile/css/styles_app.css',
                    'version' => '0.2'
                ),
            ],
            'selectuser' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'select_user',
                'displaydata' => [
                    'title' => 'selectuser',
                    'icon' => 'map',
                ],
                'priority'  => 10000000000000000,
                'styles' => array(
                    'url' => $CFG->wwwroot . '/local/coodle/mobile/css/styles_app.css',
                    'version' => '0.2'
                ),
            ],
        ],
        'lang' => [
            ['myfiles', 'local_coodle'],
            ['viewaddress', 'local_coodle'],
            ['viewtodos', 'local_coodle'],
            ['dok1', 'local_coodle'],
            ['dok2', 'local_coodle'],
            ['dok3', 'local_coodle'],
            ['todos', 'local_coodle'],
            ['viewinfo', 'local_coodle'],
        ],
    ],
];

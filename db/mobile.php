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
                    'icon' => 'files',
                ],
                'priority'  => 10000000000000000,
            ],
            'files2' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_files2',
                'displaydata' => [
                    'title' => 'dok2',
                    'icon' => 'files',
                ],
                'priority'  => 10000000000000000,
            ],
            'files3' => [
                'delegate' => 'CoreMainMenuDelegate',
                'method' => 'view_files3',
                'displaydata' => [
                    'title' => 'dok3',
                    'icon' => 'files',
                ],
                'priority'  => 10000000000000000,
            ],
            'viewaddress' => [
                'delegate' => 'CoreMainMenuHomeDelegate',
                'method' => 'view_address',
                'displaydata' => [
                    'title' => 'viewaddress',
                    'icon' => 'map',
                ],
                'priority'  => 10000000000000000,
            ],
            'viewdates' => [
                'delegate' => 'CoreMainMenuHomeDelegate',
                'method' => 'view_dates',
                'displaydata' => [
                    'title' => 'viewdates',
                    'icon' => 'map',
                ],
                'priority'  => 10000000000000000,
            ],
        ],
        'lang' => [
            ['myfiles', 'local_coodle'],
            ['viewaddress', 'local_coodle'],
            ['dok1', 'local_coodle'],
            ['dok2', 'local_coodle'],
            ['dok3', 'local_coodle'],

        ],

    ],
];
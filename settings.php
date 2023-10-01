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
 * Cohorts module admin settings and defaults
 *
 * @package local_coodle
 * @copyright  2022 Thomas Winkler (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$componentname = 'local_coodle';

// Default for users that have site config.
if ($hassiteconfig) {
    // Add the category to the local plugin branch.
    $settings = new admin_settingpage('Coodle', '');
    $ADMIN->add('localplugins', new admin_category($componentname, get_string('pluginname', $componentname)));
    $ADMIN->add($componentname, $settings);
    // Course ID of Webinarcourse.
    $settings->add(
        new admin_setting_configtext(
            $componentname .'/coodleadvisorcourseid',
            get_string('coodleadvisorcourse', $componentname),
            get_string('coodleadvisorcourseid:description', $componentname),
            0,
            PARAM_INT
        )
    );

    // TODO: Date
    $settings->add(
        new admin_setting_configtext(
            $componentname .'/coodle',
            get_string('coodledeleteusertime', $componentname),
            get_string('coodledeleteusertime:description', $componentname),
            0,
            PARAM_INT
        )
    );
}

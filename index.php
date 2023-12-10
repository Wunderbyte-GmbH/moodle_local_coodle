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
 * index
 *
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
use local_coodle\local\views\secondary;

$delid = optional_param('del', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

$secondarynav = new secondary($PAGE);
$secondarynav->initialise();
$PAGE->set_secondarynav($secondarynav);
$PAGE->set_secondary_navigation(true);

$PAGE->set_url(new moodle_url('/local/coodle/index.php', []));
$PAGE->set_pagelayout('standard');
$title = "COOdLe Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
$templatedata = [];
$templatedata['countusers'] = $DB->count_records('local_coodle_user', null);
$templatedata['countadvisors'] = $DB->count_records('local_coodle_advisor', null);

echo $OUTPUT->render_from_template('local_coodle/admin', $templatedata);
echo $OUTPUT->footer();

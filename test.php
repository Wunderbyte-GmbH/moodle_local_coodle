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
 * Testfile for developing
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('../../course/lib.php');

use local_coodle\form\user_create_form;
use local_coodle\local\views\secondary;

$delid = optional_param('del', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

$PAGE->set_url(new moodle_url('/local/coodle/index.php', array()));

$title = "cOOdle Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);


echo $OUTPUT->header();
$a = local_coodle\advisor::is_advisor(14);
if ($a) {
    echo "<h1>IS ADVISOR</h1>";
}
else {
    echo "<h1>IS NOT ADVISOR</h1>";
}
echo $OUTPUT->footer();

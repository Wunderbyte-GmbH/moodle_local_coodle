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
use local_coodle\local\views\secondary;

$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

require_once($CFG->dirroot.'/calendar/lib.php');

$PAGE->set_url(new moodle_url('/local/coodle/calendar.php', array()));
$secondarynav = new secondary($PAGE);
$secondarynav->initialise();
$PAGE->set_secondarynav($secondarynav);
$PAGE->set_secondary_navigation(true);
$title = "Calendar";
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

global $CFG;

require_once($CFG->dirroot.'/calendar/lib.php');

$content = new stdClass;
$content->footer = '';

$renderer = $PAGE->get_renderer('core_calendar');
$content->text = $renderer->start_layout();

$courseid = \local_coodle\advisor::get_advisor_course($USER->id);
$categoryid = ($PAGE->context->contextlevel === CONTEXT_COURSECAT && !empty($PAGE->category)) ?
$PAGE->category->id : null;
$calendar = \calendar_information::create(time(), $courseid, $categoryid);
list($data, $template) = calendar_get_view($calendar, 'monthblock', isloggedin());


$test = \core_calendar\local\api::get_events(
    time(),
    null,
    null,
    null,
    null,
    null,
    20,
    'group',
);
$renderer = $PAGE->get_renderer('core_calendar');
$content->text .= $renderer->render_from_template($template, $data);

$options = [
    'showfullcalendarlink' => true
];
list($footerdata, $footertemplate) = calendar_get_footer_options($calendar, $options);
$content->footer .= $renderer->render_from_template($footertemplate, $footerdata);
$content->text .= $renderer->complete_layout();

//$PAGE->requires->js_call_amd('core_calendar/popover');

echo $content->text;

$calendarexport = "<div class='container'><div class='mt-2'><a href='/calendar/export.php?' class='btn btn-primary' traget='_blank'>Export</a></div></div>";

echo $calendarexport;

$PAGE->requires->js_call_amd('local_coodle/calendarinterval', 'init');

echo $OUTPUT->footer();

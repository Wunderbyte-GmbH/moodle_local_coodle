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
 * Show QR Code of coodle advisor.
 *
 * @package    local_coodle
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
use local_coodle\local\views\secondary;
use local_coodle\permission;
use local_coodle\settings_manager;

$delid = optional_param('userid', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();
permission::require_is_advisor();

$secondarynav = new secondary($PAGE);
$secondarynav->initialise();
$PAGE->set_secondarynav($secondarynav);
$PAGE->set_secondary_navigation(true);

$PAGE->set_url(new moodle_url('/local/coodle/myqrcode.php', []));
$PAGE->set_pagelayout('standard');
$title = "My QR Code";
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();
global $USER;

$settingsmanager = new settings_manager(COODLEADVISOR);
$settingsmanager->load_user();

$qrcode = \local_coodle\overrides\mobileapioverrides::generate_login_qrcode_from_userid($settingsmanager->get_token(), $USER->id);

$templatedata['qrcode'] = $qrcode;

echo $OUTPUT->render_from_template('local_coodle/myqrcode', $templatedata);
echo $OUTPUT->footer();

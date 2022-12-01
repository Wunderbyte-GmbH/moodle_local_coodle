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

$context = \context_system::instance();
$PAGE->set_context($context);
require_login();

$PAGE->set_url(new moodle_url('/local/coodle/index.php', array()));

$title = "cOOdle Manager";
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
/**PUT IN A FUNCTION */
$qrcodeforappstr = get_string('qrcodeformobileappaccess', 'tool_mobile');

$mobilesettings = get_config('tool_mobile');
$mobilesettings->qrcodetype = local_coodle\overrides\mobileapioverrides::QR_CODE_LOGIN;
$qrcodeimg = local_coodle\overrides\mobileapioverrides::generate_login_qrcode_from_userid($mobilesettings, 130);
$mobileqr .= html_writer::link('#qrcode', get_string('viewqrcode', 'tool_mobile'),
    ['class' => 'btn btn-primary mt-2', 'data-toggle' => 'collapse',
    'role' => 'button', 'aria-expanded' => 'false']);
$mobileqr .= html_writer::div(html_writer::img($qrcodeimg, $qrcodeforappstr), 'collapse mt-4', ['id' => 'qrcode']);
echo $mobileqr;

echo $OUTPUT->footer();



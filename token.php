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
 * Return token
 * @package    local_coodle
 * @copyright  2023 Thomas Winkler based on original token.php fomr /login/token.php 2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_coodle\settings_manager;
use tool_admin_presets\local\action\load;

define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require_once('../../config.php');
require_once($CFG->libdir . '/externallib.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

$userid = required_param('userid', PARAM_INT);
$serviceshortname  = required_param('service',  PARAM_ALPHANUMEXT);
$coodlesessionkey  = required_param('coodlesessionkey',  PARAM_ALPHANUMEXT);

$systemcontext = context_system::instance();

// Load advisor or user.
if (settings_manager::is_advisor($userid)) {
    $coodleuser = new settings_manager(COODLEADVISOR);
} else {
    $coodleuser = new settings_manager(COODLEUSER);
}

$currentcoodleuser = $coodleuser->load_user($userid);
if (empty($coodleuser)) {
    throw new moodle_exception('coodleuser', 'coodleuser');
}

if (!$coodleuser->compare_token($coodlesessionkey)) {
    throw new moodle_exception('coodleuser', 'coodleuser');
}

$reason = null;
$user = \core_user::get_user($userid);

if (!empty($user)) {

    // Cannot authenticate unless maintenance access is granted.
    $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', $systemcontext, $user);
    if (!empty($CFG->maintenance_enabled) && !$hasmaintenanceaccess) {
        throw new moodle_exception('sitemaintenance', 'admin');
    }

    if (isguestuser($user)) {
        throw new moodle_exception('noguest');
    }
    if (empty($user->confirmed)) {
        throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
    }
    // Check credential expiry.
    $userauth = get_auth_plugin($user->auth);
    if (!empty($userauth->config->expiration) && $userauth->config->expiration == 1) {
        $days2expire = $userauth->password_expire($user->username);
        if (intval($days2expire) < 0 ) {
            throw new moodle_exception('passwordisexpired', 'webservice');
        }
    }

    // Let enrol plugins deal with new enrolments if necessary.
    enrol_check_plugins($user);

    // Setup user session to check capability.
    \core\session\manager::set_user($user);

    // Check if the service exists and is enabled.
    $service = $DB->get_record('external_services', ['shortname' => $serviceshortname, 'enabled' => 1]);
    if (empty($service)) {
        // Will throw exception if no token found.
        throw new moodle_exception('servicenotavailable', 'webservice');
    }

    // Get an existing token or create a new one.
    $token = external_generate_token_for_current_user($service);
    $privatetoken = $token->privatetoken;
    external_log_token_request($token);

    $siteadmin = has_capability('moodle/site:config', $systemcontext, $USER->id);

    $usertoken = new stdClass;
    $usertoken->token = $token->token;
    // Private token, only transmitted to https sites and non-admin users.
    if (!$siteadmin) {
        $usertoken->privatetoken = $privatetoken;
    } else {
        $usertoken->privatetoken = null;
    }
    echo json_encode($usertoken);

    $coodleuser->renew_token();

} else {
    throw new moodle_exception('invalidlogin');
}

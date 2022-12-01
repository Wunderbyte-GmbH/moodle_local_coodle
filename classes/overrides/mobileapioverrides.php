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

namespace local_coodle\overrides;

use stdClass;
use core_qrcode;

/*
 * Mobile API overrides tool_mobile
 * @package    local_coodle
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobileapioverrides extends \tool_mobile\api {
    /**
     * Creates a QR login key for the current user, this key is restricted by time and ip address.
     * This key is used for automatically login the user in the site when the user scans a QR code in the Moodle app.
     *
     * @param  stdClass $mobilesettings  mobile app plugin settings
     * @param int $userid
     * @return string the key
     */
    public static function get_qrlogin_key_from_userid(stdClass $mobilesettings, int $userid) {
        // Delete previous keys.
        delete_user_key('tool_mobile', $userid);

        // Create a new key.
        $iprestriction = getremoteaddr(null);
        $qrkeyttl = !empty($mobilesettings->qrkeyttl) ? $mobilesettings->qrkeyttl : self::LOGIN_QR_KEY_TTL;
        $validuntil = time() + $qrkeyttl;
        return create_user_key('tool_mobile', $userid, null, $iprestriction, $validuntil);
    }


    /**
     * Generates a QR code with the site URL or for automatic login from the mobile app.
     *
     * @param  stdClass $mobilesettings tool_mobile settings
     * @return string base64 data image contents, null if qr disabled
     */
    public static function generate_login_qrcode_from_userid(stdClass $mobilesettings, int $userid) {
        global $CFG;

        if ($mobilesettings->qrcodetype == static::QR_CODE_DISABLED) {
            return null;
        }

        $urlscheme = !empty($mobilesettings->forcedurlscheme) ? $mobilesettings->forcedurlscheme : 'moodlemobile';
        $data = $urlscheme . '://' . $CFG->wwwroot;

        if ($mobilesettings->qrcodetype == static::QR_CODE_LOGIN) {
            $qrloginkey = static::get_qrlogin_key_from_userid($mobilesettings, $userid);
            $data .= '?qrlogin=' . $qrloginkey . '&userid=' . $userid;
        }

        $qrcode = new core_qrcode($data);
        $imagedata = 'data:image/png;base64,' . base64_encode($qrcode->getBarcodePngData(5, 5));

        return $imagedata;
    }
}

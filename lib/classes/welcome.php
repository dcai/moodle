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
 * This file contains the core_welcome class
 *
 * @package    core
 * @copyright  Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This Class contains helper functions for welcome message.
 *
 * @copyright  Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_welcome
{
    /**
     * @var string User preference key to save sesskey
     */
    private const USER_PREFERENCE_NAME = 'core_welcome_sesskey';

    /**
     * Displays welcome message.
     */
    public static function display_welcome_message(): void {
        global $USER;
        $level = \core\notification::INFO;
        $currentsesskey = sesskey();
        if (!isloggedin() || isguestuser()) {
            return;
        }

        if (self::is_first_login()) {
            \core\notification::add(get_string('welcometosite', 'moodle', fullname($USER)), $level);
            set_user_preferences([self::USER_PREFERENCE_NAME => $currentsesskey]);
            return;
        }

        if (self::should_display_welcome_back()) {
            \core\notification::add(get_string('welcomeback', 'moodle', fullname($USER)), $level);
            set_user_preferences([self::USER_PREFERENCE_NAME => $currentsesskey]);
        }
    }

    /**
     * Is this the first access to moodle?
     */
    private static function is_first_login(): bool {
        global $USER;
        $isfirstlogin = empty($USER->firstaccess);
        $savedsession = get_user_preferences(self::USER_PREFERENCE_NAME);

        return $isfirstlogin || empty($savedsession);
    }

    /**
     * Is this the first page after login?
     */
    private static function should_display_welcome_back(): bool {
        $currentsesskey = sesskey();
        $savedsession = get_user_preferences(self::USER_PREFERENCE_NAME);
        if ($savedsession != $currentsesskey) {
            return true;
        }
        return false;
    }
}

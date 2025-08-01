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
 * This script triggers a full purging of system caches,
 * this is useful mostly for developers who did not disable the caching.
 *
 * @package    core
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('IGNORE_COMPONENT_CACHE', true);

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$confirm = optional_param('confirm', 0, PARAM_BOOL);
$returnurl = optional_param('returnurl', '/admin/purgecaches.php', PARAM_LOCALURL);
$returnurl = new moodle_url($returnurl);

admin_externalpage_setup('purgecaches');

$form = new core_admin\form\purge_caches(null, ['returnurl' => $returnurl]);

// If we have got here as a confirmed aciton, do it.
if ($data = $form->get_data()) {

    // Valid request. Purge, and redirect the user back to where they came from.
    if (isset($data->all)) {
        purge_caches();
        $message = get_string('purgecachesfinished', 'admin');
    } else {
        // When passing selected options, ensure each is cast to boolean for strict comparison.
        purge_caches(array_map(
            fn($option) => (bool) $option,
            $data->purgeselectedoptions,
        ));
        $message = get_string('purgeselectedcachesfinished', 'admin');
    }

} else if ($confirm && confirm_sesskey()) {
    purge_caches();
    $message = get_string('purgecachesfinished', 'admin');
}

// Redirect and/or show notification message confirming cache(s) were purged.
if (isset($message)) {
    if (!$PAGE->url->compare($returnurl, URL_MATCH_BASE)) {
        redirect($returnurl, $message);
    }

    // We are already on the purge caches page, add the notification.
    \core\notification::add($message, \core\output\notification::NOTIFY_INFO);
}

// Otherwise, show a form to actually purge the caches.

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('purgecachespage', 'admin'));

echo $OUTPUT->box_start('generalbox', 'notice');
echo html_writer::tag('p', get_string('purgecachesconfirm', 'admin'));
echo $form->render();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

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
 * Marklar editor user preferences
 *
 * @package     editor_marklar
 * @copyright   2016 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once($CFG->dirroot.'/user/editlib.php');

require_login(SITEID, false);

$userid = optional_param('userid', $USER->id, PARAM_INT);

list($user, $course) = useredit_setup_preference_page($userid, SITEID);

$PAGE->set_url('/lib/editor/marklar/preferences.php', ['id' => $userid]);
$PAGE->navbar->includesettingsbase = true;
$PAGE->set_title($course->shortname.': '.get_string('preferences', 'editor_marklar'));
$PAGE->set_heading(fullname($user, true));

$form = new editor_marklar_preferences_form(null, ['user' => $user]);
$data = [
    'monospace' => get_user_preferences('editor_marklar/monospace', false, $user),
];

$formats = json_decode(get_user_preferences('editor_marklar/formats', '', $user));
if (is_object($formats)) {
    $data = array_merge($data, (array)$formats);
}

$form->set_data($data);

$redirect = new moodle_url('/user/preferences.php', ['userid' => $user->id]);

if ($form->is_cancelled()) {
    redirect($redirect);

} else if ($data = $form->get_data()) {
    $formats = [
        'format'.FORMAT_MOODLE => !empty($data->{'format'.FORMAT_MOODLE}),
        'format'.FORMAT_HTML => !empty($data->{'format'.FORMAT_HTML}),
        'format'.FORMAT_PLAIN => !empty($data->{'format'.FORMAT_PLAIN}),
    ];
    set_user_preference('editor_marklar/formats', json_encode($formats), $user);
    set_user_preference('editor_marklar/monospace', !empty($data->monospace));
    \core\event\user_updated::create_from_userid($user->id)->trigger();
    redirect($redirect);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('preferences', 'editor_marklar'));
$form->display();
echo $OUTPUT->footer();

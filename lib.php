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
 * Text editor interface implementation.
 *
 * @package     editor_marklar
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the Marklar editor behaviour in terms of Moodle text editor interface
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marklar_texteditor extends texteditor {

    /**
     * Is editor supported in current browser?
     *
     * @return bool
     */
    public function supported_by_browser() {
        return true;
    }

    /**
     * Returns list of supported text formats.
     *
     * Marklar natively supports Markdown texts. As it is basically just a
     * textarea, we also declare it should be used for Moodle auto-formatted
     * and plain texts. For HTML, another rich text editor would be then used
     * (such as Atto).  However, users can set this behaviour via their
     * preferences.
     *
     * @return array
     */
    public function get_supported_formats() {

        // Marklar is always supported.
        $supported = [FORMAT_MARKDOWN => FORMAT_MARKDOWN];

        // Other formats can be supported via user preferences.
        $formats = json_decode(get_user_preferences('editor_marklar/formats', ''));

        if (is_object($formats)) {
            if (!empty($formats->{'format'.FORMAT_MOODLE})) {
                $supported[FORMAT_MOODLE] = FORMAT_MOODLE;
            }
            if (!empty($formats->{'format'.FORMAT_HTML})) {
                $supported[FORMAT_HTML] = FORMAT_HTML;
            }
            if (!empty($formats->{'format'.FORMAT_PLAIN})) {
                $supported[FORMAT_PLAIN] = FORMAT_PLAIN;
            }

        } else {
            $supported[FORMAT_PLAIN] = FORMAT_PLAIN;
            $supported[FORMAT_MOODLE] = FORMAT_MOODLE;
        }

        return $supported;
    }

    /**
     * Returns main preferred text format.
     *
     * @return int text format
     */
    public function get_preferred_format() {
        return FORMAT_MARKDOWN;
    }

    /**
     * Supports file picker and repos?
     *
     * @return bool
     */
    public function supports_repositories() {
        return false;
    }

    /**
     * Add required JS needed for editor
     *
     * @param string $elementid id of text area to be converted to editor
     * @param array|null $options
     * @param object $fpoptions file picker options
     * @return void
     */
    public function use_editor($elementid, array|null $options = null, $fpoptions = null) {
        global $PAGE;

        $initparams = [
            'elementid' => $elementid,
            'contextid' => empty($options['context']) ? $PAGE->context->id : $options['context']->id,
            'monospace' => !empty(get_user_preferences('editor_marklar/monospace')),
        ];

        $PAGE->requires->js_call_amd('editor_marklar/editor', 'init', [$initparams]);

        // If we passed the $fpoptions via init's parameters, debugging warning
        // about the parameter size would be thrown. This is a really nasty
        // hack to work around that. See MDL-53423 for details.
        $PAGE->requires->js_init_code('M.editor_marklar = M.editor_marklar || {}');
        $PAGE->requires->js_init_code('M.editor_marklar.fpoptions = M.editor_marklar.fpoptions || {}');
        $PAGE->requires->js_init_code(js_writer::set_variable('M.editor_marklar.fpoptions['.json_encode($elementid).']',
            convert_to_array($fpoptions)));
    }
}


/**
 * Extends the user preferences page
 *
 * @param navigation_node $usersetting
 * @param stdClass $user
 * @param context_user $usercontext
 * @param stdClass $course
 * @param context_course $coursecontext
 */
function editor_marklar_extend_navigation_user_settings(navigation_node $usersetting, $user, context_user $usercontext,
        $course, context_course $coursecontext) {
    global $CFG;

    // Check if the user's preferred editor is Marklar.
    $preference = get_user_preferences('htmleditor', null, $user);

    // If the user's preferred editor is "default", check if it is Marklar.
    if (empty($preference) && !empty($CFG->texteditors)) {
        $editors = explode(',', $CFG->texteditors);
        if (reset($editors) === 'marklar') {
            $preference = 'marklar';
        }
    }

    // If the user's preferred editor is Marklar, show a link to Marklar preferences page.
    if ($preference === 'marklar') {
        $prefurl = new moodle_url('/lib/editor/marklar/preferences.php', ['userid' => $user->id]);
        $usersetting->add(get_string('preferences', 'editor_marklar'), $prefurl);
    }
}

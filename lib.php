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

defined('MOODLE_INTERNAL') || die();

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
     * We intentionally do not declare support for HTML here. So when editing
     * existing text in FORMAT_HTML, another editor would be used (such as Atto).
     *
     * @return array
     */
    public function get_supported_formats() {
        return [
            FORMAT_MARKDOWN => FORMAT_MARKDOWN,
            FORMAT_PLAIN => FORMAT_PLAIN,
            FORMAT_MOODLE => FORMAT_MOODLE,
        ];
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
     * @todo
     * @return bool
     */
    public function supports_repositories() {
        return false;
    }

    /**
     * Add required JS needed for editor
     *
     * @param string $elementid id of text area to be converted to editor
     * @param array $options
     * @param object $fpoptions file picker options
     * @todo
     * @return void
     */
    public function use_editor($elementid, array $options=null, $fpoptions = null) {
        global $PAGE;

        $initparams = [
            'elementid' => $elementid,
            'contextid' => empty($options['context']) ? $PAGE->context->id : $options['context']->id,
            'filepickeroptions' => empty($fpoptions) ? [] : $fpoptions,
        ];

        $PAGE->requires->js_call_amd('editor_marklar/editor', 'init', [$initparams]);
    }
}

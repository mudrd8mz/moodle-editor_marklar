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
 * Provides the class {@link editor_marklar_external}.
 *
 * @package     editor_marklar
 * @category    external
 * @copyright   2016 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/externallib.php');

/**
 * Provides the plugin external functions.
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_marklar_external extends external_api {

    /**
     * Describes the input parameters for the {@link self::get_preview()}.
     *
     * @return external_function_parameters
     */
    public static function get_preview_parameters() {
        return new external_function_parameters([
            'text' => new external_value(PARAM_RAW, 'Input text', VALUE_REQUIRED),
            'format' => new external_value(PARAM_INT, 'Format of the input text', VALUE_REQUIRED),
            'contextid' => new external_value(PARAM_INT, 'Context where the text will appear', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns the formatted text for preview.
     *
     * @param string $text
     * @param int $format
     * @param int $contextid
     * @return string
     */
    public static function get_preview($text, $format, $contextid) {
        global $CFG;

        extract(self::validate_parameters(self::get_preview_parameters(), compact('text', 'format', 'contextid')));

        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // We do not really know what exact options will be used. Guess
        // something reasonable for our purpose here.
        $options = [
            'context' => $context,
            'nocache' => true,
            'para' => false,
            'blanktarget' => true,
        ];

        // Make sure the draftfile.php links are not replaced with brokenfile.php links.
        $text = str_replace($CFG->httpswwwroot.'/draftfile.php', '@@DRAFTFILE@@', $text);
        $html = format_text($text, $format, $options);
        $html = str_replace('@@DRAFTFILE@@', $CFG->httpswwwroot.'/draftfile.php', $html);

        return [
            'html' => $html,
        ];
    }

    /**
     * Describes the value returned by the {@link self::get_preview()}.
     *
     * @return external_single_structure
     */
    public static function get_preview_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML formatted text to be displayed'),
        ]);
    }
}

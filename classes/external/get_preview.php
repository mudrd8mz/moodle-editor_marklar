<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace editor_marklar\external;

use context;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Provides the plugin external functions.
 *
 * @package     editor_marklar
 * @category    external
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_preview extends external_api {

    /**
     * Describes the input parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
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
    public static function execute($text, $format, $contextid) {
        global $CFG;

        // @codingStandardsIgnoreLine
        extract(self::validate_parameters(self::execute_parameters(), compact('text', 'format', 'contextid')));

        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // We do not really know what exact options will be used. Guess
        // something reasonable for our purpose here.
        $options = [
            'context' => $context,
            'para' => false,
            'blanktarget' => true,
        ];

        // Make sure the draftfile.php links are not replaced with brokenfile.php links.
        $text = str_replace($CFG->wwwroot.'/draftfile.php', '@@DRAFTFILE@@', $text);
        $html = format_text($text, $format, $options);
        $html = str_replace('@@DRAFTFILE@@', $CFG->wwwroot.'/draftfile.php', $html);

        return [
            'html' => $html,
        ];
    }

    /**
     * Describes the returned value.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'HTML formatted text to be displayed'),
        ]);
    }
}

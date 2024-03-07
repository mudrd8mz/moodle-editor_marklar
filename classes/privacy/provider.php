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
 * Provides the {@see editor_marklar\privacy\provider} class.
 *
 * @package     editor_marklar
 * @category    privacy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace editor_marklar\privacy;

use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\transform;
use core_privacy\local\legacy_polyfill;

/**
 * Provides a response to a specific privacy related request from the user.
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This plugin stores user private data.
        \core_privacy\local\metadata\provider,

        // This plugin stores site-wide user preferences.
        \core_privacy\local\request\user_preference_provider {

    /**
     * Collect metadata describing personal data stored by the plugin.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('editor_marklar/formats', 'preferencesformat');
        $collection->add_user_preference('editor_marklar/monospace', 'preferencesmonospace');
        return $collection;
    }

    /**
     * Export all user preferences controlled by the plugin.
     *
     * @param int $userid The id of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {

        $raw = (array) json_decode(get_user_preferences('editor_marklar/formats', '', $userid));

        if (is_array($raw)) {
            foreach ($raw as $format => $enabled) {
                switch($format) {
                    case 'format'.FORMAT_HTML:
                        $name = get_string('formathtml');
                        break;
                    case 'format'.FORMAT_MOODLE:
                        $name = get_string('formattext');
                        break;
                    case 'format'.FORMAT_PLAIN:
                        $name = get_string('formatplain');
                        break;
                    default:
                        // This is not expected to happen.
                        $name = $format;
                }

                $desc = get_string('privacy:export:preferences:format', 'editor_marklar', ['format' => $name]);

                writer::export_user_preference('editor_marklar', 'formats.'.$format, transform::yesno($enabled), $desc);
            }
        }
    }
}

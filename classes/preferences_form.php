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
 * Provides the editor_marklar_preferences_form class.
 *
 * @package     editor_marklar
 * @copyright   2016 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Defines the Marklar editor user preferences form.
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_marklar_preferences_form extends moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addGroup([
            $mform->createElement('checkbox', 'format'.FORMAT_HTML, null, get_string('formathtml')),
            $mform->createElement('checkbox', 'format'.FORMAT_MOODLE, null, get_string('formattext')),
            $mform->createElement('checkbox', 'format'.FORMAT_PLAIN, null, get_string('formatplain')),
        ], 'formats', get_string('preferencesformat', 'editor_marklar'), '<br>', false);

        $mform->addHelpButton('formats', 'preferencesformat', 'editor_marklar');
        $mform->setDefault('format'.FORMAT_HTML, 0);
        $mform->setDefault('format'.FORMAT_MOODLE, 1);
        $mform->setDefault('format'.FORMAT_PLAIN, 1);

        $mform->addGroup([
            $mform->createElement('checkbox', 'monospace', get_string('preferencesmonospace', 'editor_marklar')),
        ], 'editing', get_string('preferencesediting', 'editor_marklar'), '<br>', false);

        if (!empty($this->_customdata['user'])) {
            $mform->addElement('hidden', 'userid', $this->_customdata['user']->id);
            $mform->setType('userid', PARAM_INT);
        }

        $this->add_action_buttons();
    }
}

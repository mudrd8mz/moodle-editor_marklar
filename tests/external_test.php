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
 * Provides {@link editor_marklar_external_testcase} class.
 *
 * @package     editor_marklar
 * @category    test
 * @copyright   2016 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/externallib.php');

/**
 * Defines tests for the plugin external API.
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_marklar_external_testcase extends advanced_testcase {

    /**
     * Test the editor_marklar_get_preview() external function.
     */
    public function test_get_preview() {
        $this->resetAfterTest();

        $this->setUser($this->getDataGenerator()->create_user());
        $syscontext = context_system::instance();

        $text = '## It works! ##';
        $format = FORMAT_MARKDOWN;
        $contextid = $syscontext->id;

        $response = external_api::clean_returnvalue(editor_marklar_external::get_preview_returns(),
            editor_marklar_external::get_preview($text, $format, $contextid));

        $this->assertEquals('<h2>It works!</h2>', $response['html']);
    }
}

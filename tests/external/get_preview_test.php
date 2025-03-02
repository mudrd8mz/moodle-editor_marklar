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

namespace editor_marklar\external;

/**
 * Defines tests for the plugin external function editor_marklar_get_preview.
 *
 * @package     editor_marklar
 * @covers      \editor_marklar\external\get_preview
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_preview_test extends \advanced_testcase {

    /**
     * Test the editor_marklar_get_preview() external function.
     *
     * @runInSeparateProcess
     */
    public function test_get_preview(): void {
        global $CFG;
        require_once($CFG->libdir.'/externallib.php');

        $this->resetAfterTest();

        $this->setUser($this->getDataGenerator()->create_user());
        $syscontext = \context_system::instance();

        $text = '## It works! ##';
        $format = FORMAT_MARKDOWN;
        $contextid = $syscontext->id;

        $response = \core_external\external_api::clean_returnvalue(get_preview::execute_returns(),
            get_preview::execute($text, $format, $contextid));

        $this->assertEquals('<h2>It works!</h2>', $response['html']);
    }

    /**
     * Test that draftfile.php links work in preview.
     *
     * @runInSeparateProcess
     */
    public function test_embeded_images_preview(): void {
        global $CFG;
        require_once($CFG->libdir.'/externallib.php');

        $this->resetAfterTest();

        $this->setUser($this->getDataGenerator()->create_user());
        $syscontext = \context_system::instance();

        $text = '* <img src="'.$CFG->wwwroot.'/draftfile.php/5/user/draft/179426321/test.png">';
        $format = FORMAT_HTML;
        $contextid = $syscontext->id;

        $response = \core_external\external_api::clean_returnvalue(get_preview::execute_returns(),
            get_preview::execute($text, $format, $contextid));

        $this->assertFalse(strpos($response['html'], 'brokenfile.php'));

    }
}

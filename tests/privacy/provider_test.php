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

namespace editor_marklar\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\deletion_criteria;

/**
 * Unit tests for the privacy API.
 *
 * @package     editor_marklar
 * @covers      \editor_marklar\privacy\provider
 * @copyright   2018 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \advanced_testcase {

    /**
     * Assert that provider::get_metadata() returns valid content.
     */
    public function test_get_metadata(): void {

        $items = new collection('editor_marklar');
        $result = provider::get_metadata($items);
        $this->assertSame($items, $result);
        $this->assertInstanceOf(collection::class, $result);
    }

    /**
     * Assert that provider::export_user_preferences() gives no data if the user has no Marklar preferences.
     */
    public function test_export_user_preferences_no_pref(): void {

        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());

        $this->assertFalse($writer->has_any_data());
    }


    /**
     * Assert that provider::export_user_preferences() gives user's Marklar preferences if they exist.
     */
    public function test_export_user_preferences_has_pref(): void {
        $this->resetAfterTest();

        $user = \core_user::get_user_by_username('admin');
        $formats = [
            'format'.FORMAT_MOODLE => true,
            'format'.FORMAT_HTML => false,
            'format'.FORMAT_PLAIN => true,
        ];
        set_user_preference('editor_marklar/formats', json_encode($formats), $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        $prefs = (array) $writer->get_user_preferences('editor_marklar');

        $this->assertEquals(3, count($prefs));
        $this->assertArrayHasKey('formats.format'.FORMAT_MOODLE, $prefs);
        $this->assertArrayHasKey('formats.format'.FORMAT_HTML, $prefs);
        $this->assertArrayHasKey('formats.format'.FORMAT_PLAIN, $prefs);
    }
}

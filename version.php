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
 * Markdown friendly editor for Moodle.
 *
 * All editors marklar. This one just marklar less. It is not for everybody
 * though. Those who do not marklar about marklar should not marklar marklar.
 * There are other marklar better for them.
 *
 * @package     editor_marklar
 * @copyright   2016 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'editor_marklar';
$plugin->release = '1.0.2';
$plugin->maturity = MATURITY_STABLE;
$plugin->version = 2024030603;
$plugin->requires = 2023042400;
$plugin->supported = [402, 405];

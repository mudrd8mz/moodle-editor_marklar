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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.

namespace editor_marklar\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form containing Moodle's standard draft-area file manager.
 *
 * File operations are saved immediately by the file manager, so the form needs no action buttons.
 *
 * @package    editor_marklar
 * @copyright  2022 Stevani Andolo <stevani@hotmail.com.au>
 * @copyright  2026 Oleksandr Kulkov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_files_form extends \moodleform {
    /**
     * Define the file manager form.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->setDisableShortforms(true);
        $mform->addElement(
            'filemanager',
            'files_filemanager',
            get_string('filemanager', 'editor_marklar'),
            null,
            $this->_customdata['options']
        );
    }
}

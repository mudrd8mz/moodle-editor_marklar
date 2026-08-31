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

/**
 * Manage files in a draft area attached to a Marklar editor.
 *
 * @package    editor_marklar
 * @copyright  2022 Stevani Andolo <stevani@hotmail.com.au>
 * @copyright  2026 Oleksandr Kulkov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/repository/lib.php');

$itemid = required_param('itemid', PARAM_INT);
$contextid = required_param('context', PARAM_INT);
$maxbytes = optional_param('maxbytes', 0, PARAM_INT);
$areamaxbytes = optional_param('areamaxbytes', FILE_AREA_MAX_BYTES_UNLIMITED, PARAM_INT);
$maxfiles = optional_param('maxfiles', -1, PARAM_INT);
$subdirs = optional_param('subdirs', 0, PARAM_INT);
$returntypes = optional_param('return_types', FILE_INTERNAL, PARAM_INT);

$context = context::instance_by_id($contextid);
if ($context->contextlevel == CONTEXT_MODULE) {
    $cm = $DB->get_record('course_modules', ['id' => $context->instanceid], '*', MUST_EXIST);
    require_login($cm->course, true, $cm);
    $PAGE->set_context($context);
} else if (($coursecontext = $context->get_course_context(false)) && $coursecontext->id != SITEID) {
    require_login($coursecontext->instanceid);
    $PAGE->set_context($context);
} else {
    require_login();
    $PAGE->set_context($context);
}

if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$url = new moodle_url('/lib/editor/marklar/manage.php', [
    'itemid' => $itemid,
    'context' => $contextid,
    'maxbytes' => $maxbytes,
    'areamaxbytes' => $areamaxbytes,
    'maxfiles' => $maxfiles,
    'subdirs' => $subdirs,
    'return_types' => $returntypes,
]);
$title = get_string('managefiles', 'editor_marklar');
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

$options = [
    'subdirs' => $subdirs,
    'maxbytes' => $maxbytes,
    'maxfiles' => $maxfiles,
    'accepted_types' => '*',
    'areamaxbytes' => $areamaxbytes,
    'return_types' => $returntypes & ~FILE_EXTERNAL,
    'context' => $context,
];

$mform = new editor_marklar\form\manage_files_form(null, ['options' => $options]);
$mform->set_data(['files_filemanager' => $itemid]);

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

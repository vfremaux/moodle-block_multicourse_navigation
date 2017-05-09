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
 * @package     block_multicourse_navigation
 * @copyright   2016 onwards Valery Fremaux <http://docs.activeprolearn.com/en>
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');

$id = required_param('id', PARAM_INT); // Current course id.
$item = required_param('item', PARAM_ALPHA); // Item can be 'course' or 'section'.
$itemid = required_param('itemid', PARAM_INT); // Itemid is the item instance id.
$blockid = required_param('blockid', PARAM_INT);
$hide = required_param('hide', PARAM_BOOL);

if (!$DB->get_record('block_instances', array('id' => $blockid))) {
    print_error('badsectionid');
}

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}

require_login($course);

if ($item == 'course') {
    $itemtable = 'course';
} else {
    $itemtable = 'course_sections';
}
if (!$DB->record_exists($itemtable, array('id' => $itemid))) {
    print_error('coursemisconf');
}

$hidekey = 'modulenavigation_'.$blockid.'_'.$item.'_'.$itemid.'_hidden';
$params = array('userid' => $USER->id, 'name' => $hidekey);
if (!$hide) {
    $DB->delete_records('user_preferences', $params);
} else {
    if ($oldrec = $DB->get_record('user_preferences', $params)) {
        // We should never have as deleting when showing.
        $oldrec->value = $course->id;
        $DB->update_record('user_preferences', $oldrec);
    } else {
        // Store course id in value to optimise retrieval.
        $newrec = new StdClass;
        $newrec->userid = $USER->id;
        $newrec->name = $hidekey;
        $newrec->value = $course->id;
        $DB->insert_record('user_preferences', $newrec);
    }
}
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

$blockid = required_param('blockid', PARAM_INT);
$action = required_param('what', PARAM_TEXT);

$blockrec = $DB->get_record('block_instances', array('id' => $blockid));
$theblock = block_instance('multicourse_navigation', $blockrec);
$context = context::get_by_id($blockrec->parentcontextid);
$id = $context->instanceid;

if (!$DB->get_record('block_instances', array('id' => $blockid))) {
    print_error('badsectionid');
}

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}

require_login($course);

if ($action == 'expandall') {
    $select = " name LIKE 'modulenavigation_{$blockid}_' ";
    $DB->delete_records_select('user_preferences', $select, array($USER->id));
} else if ($action == 'collapseall') {
    $courseids = explode(',', $block->config->trackcourses);
    foreach ($courseid as $cid) {
        $hidekey = 'modulenavigation_'.$blockid.'_course_'.$cid.'_hidden';
        $params = array('userid' => $USER->id, 'name' => $hidekey);
        if (!$DB->record_exists('user_preferences', $params)) {
            $pref = new StdClass;
            $pref->name = $hidekey;
            $pref->value = $course->id;
            $DB->insert_record('user_preferences', $pref);
        }

        $sections = $DB->get_records('course_sections', array('course' => $cid), '', 'id,id');
        if ($sections) {
            foreach ($sections as $s) {
                $hidekey = 'modulenavigation_'.$blockid.'_section_'.$s->id.'_hidden';
                $params = array('userid' => $USER->id, 'name' => $hidekey);
                if (!$DB->record_exists('user_preferences', $params)) {
                    $pref = new StdClass;
                    $pref->name = $hidekey;
                    $pref->value = $course->id;
                    $DB->insert_record('user_preferences', $pref);
                }
            }
        }
    }
    $hidekey = 'modulenavigation_'.$blockid.'_'.$item.'_'.$itemid.'_hidden';

} else if ($action == 'showmodules') {

    $params = array('userid' => $USER->id, 'name' => $hidekey);
    if (!$DB->record_exists('user_preferences', $params)) {
        $pref = new StdClass;
        $pref->name = $hidekey;
        $pref->value = $course->id;
        $DB->insert_record('user_preferences', $pref);
    }

} else if ($action == 'hidemodules') {

    $params = array('userid' => $USER->id, 'name' => $hidekey);
    if (!$DB->record_exists('user_preferences', $params)) {
        $pref = new StdClass;
        $pref->name = $hidekey;
        $pref->value = $course->id;
        $DB->insert_record('user_preferences', $pref);
    }

}

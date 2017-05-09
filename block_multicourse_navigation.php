<?php
// This file is part of The Course Module Navigation Block
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
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

/**
 * Course contents block generates a table of course contents based on the
 * section descriptions
 */
class block_multicourse_navigation extends block_base {

    /**
     * Will dynamically cache the current course completion info to share with other methods.
     */
    protected $completioninfo;

    /**
     * Will dynamically cache the current course mod info to share with other methods.
     */
    protected $modinfo;

    /**
     * Will dynamically cache the current course object to share with other methods.
     */
    protected $course;

    /**
     * Will dynamically cache the current format object to share with other methods.
     */
    protected $format;

    protected $userstates;

    /**
     * Initializes the block, called by the constructor
     */
    public function init() {

        $this->title = get_string('pluginname', 'block_multicourse_navigation');

    }

    public function has_config() {
        return true;
    }

    /**
     * Amend the block instance after it is loaded
     */
    public function specialization() {
        global $DB, $USER;

        if (!empty($this->config->blocktitle)) {
            $this->title = $this->config->blocktitle;
        } else {
            $this->title = get_string('config_blocktitle_default', 'block_multicourse_navigation');
        }

        // Fills the cache of user when block is created.
        // Request is optimised to the current course scope, using preference value.
        $select = ' userid = :userid AND '.$DB->sql_like('name', ':prefname');

        $hidekey = 'modulenavigation\\_'.$this->instance->id.'\\_course\\_%\\_hidden';
        $params = array('userid' => $USER->id, 'prefname' => $hidekey);
        $navprefs = $DB->get_records_select('user_preferences', $select, $params);

        $this->userstates = array();
        if ($navprefs) {
            foreach ($navprefs as $prf) {
                $name = str_replace('modulenavigation_'.$this->instance->id.'_course_', '', $prf->name);
                $itemid = str_replace('_hidden', '', $name);
                $this->userstates['course'][$itemid] = $prf->value;
            }
        }

        $hidekey = 'modulenavigation\\_'.$this->instance->id.'\\_section\\_%\\_hidden';
        $params = array('userid' => $USER->id, 'prefname' => $hidekey);
        $navprefs = $DB->get_records_select('user_preferences', $select, $params);

        if ($navprefs) {
            foreach ($navprefs as $prf) {
                $name = str_replace('modulenavigation_'.$this->instance->id.'_section_', '', $prf->name);
                $itemid = str_replace('_hidden', '', $name);
                $this->userstates['section'][$itemid] = $prf->value;
            }
        }
    }

    /**
     * Which page types this block may appear on
     * @return array
     */
    public function applicable_formats() {
        return array('site-index' => true, 'course-view-*' => true);
    }

    /**
     * Returns the navigation
     *
     * @return navigation_node The navigation object to display
     */
    protected function get_navigation() {
        $this->page->navigation->initialise();
        return clone($this->page->navigation);
    }

    /**
     * Populate this block's content object
     * @return stdClass block content info
     */
    public function get_content() {
        global $DB, $OUTPUT, $COURSE, $CFG, $USER;

        if (!is_null($this->content)) {
            return $this->content;
        }

        if (empty($this->config)) {
            $this->config = new StdClass;
            $this->config->showmodules = true;
            $this->config->usecompletion = 'off';
            $this->config->showsectionlinks = true;
        }

        $selected = optional_param('section', null, PARAM_INT);
        $intab = optional_param('dtab', null, PARAM_TEXT);
        $thiscontext = context::instance_by_id($this->page->context->id);

        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (empty($this->config->trackcourses)) {
            // Defaults to current course.
            if (empty($this->config)) {
                $this->config = new StdClass;
            }
            $this->config->trackcourses = $this->page->course->id;
        }

        $courses = explode(',', $this->config->trackcourses);

        $template = new stdClass();
        $template->showsectionlinks = @$this->config->showsectionlinks;

        $this->completionok = array(COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS);

        $this->hicons['collapsed'] = $OUTPUT->pix_url('collapsed', 'block_multicourse_navigation');
        $this->hicons['expanded'] = $OUTPUT->pix_url('expanded', 'block_multicourse_navigation');
        $this->hicons['collapsednosubs'] = $OUTPUT->pix_url('collapsed_nosubs', 'block_multicourse_navigation');
        $this->hicons['expandednosubs'] = $OUTPUT->pix_url('expanded_nosubs', 'block_multicourse_navigation');
        $this->hicons['default'] = $OUTPUT->pix_url('leaf', 'block_multicourse_navigation');

        foreach ($courses as $courseid) {

            $this->format = course_get_format($courseid);

            if (!$this->format->uses_sections()) {
                if (debugging()) {
                    $this->content->text .= '<div>'.get_string('notusingsections', 'block_multicourse_navigation').'</div>';
                }
                continue;
            }

            $sections = $this->format->get_sections();
            if (empty($sections)) {
                continue;
            }

            $this->currentsectionid = optional_param('section', 0, PARAM_INT);

            $this->course = $DB->get_record('course', array('id' => $courseid)); // Needed to have numsections property available.
            $params = array('courseid' => $this->course->id, 'name' => 'numsections');
            $this->course->numsections = $DB->get_field('course_format_options', 'value', $params);

            $coursetpl = new StdClass;
            $coursetpl->coursename = format_string($this->course->fullname);
            $coursetpl->courseid = $this->course->id;

            // This is just the initial default state at loading.
            if ($COURSE->id == $this->course->id) {
                $coursetpl->currentclass = 'current-course';
            } else {
                $state = 'collapsed';
                $coursetpl->currentclass = 'other';
            }

            if (empty($this->userstates['course'][$this->course->id])) {
                $state = 'expanded';
            } else {
                $state = 'collapsed';
                $coursetpl->initial = 'style="display: none;visibility: hidden"';
            }

            $courseurl = new moodle_url('/course/view.php', array('id' => $this->course->id));
            $coursetpl->courseurl = $courseurl->out();
            $coursetpl->blockid = $this->instance->id;
            $coursetpl->handle = html_writer::img($this->hicons[$state], get_string($state, 'block_multicourse_navigation'));

            $this->context = context_course::instance($this->course->id);

            if (is_enrolled($this->context, $USER)) {
                $coursetpl->courseaccess = true;
            } else {
                $coursetpl->courseaccess = false;
            }

            $this->modinfo = get_fast_modinfo($this->course);

            // Get course completion information from completion source.
            $coursetpl->completionon = 'off';
            if (@$this->config->usecompletion == 1) {
                $this->completioninfo = new completion_info($this->course);
                if ($this->completioninfo->is_enabled()) {
                    $coursetpl->completionon = 'completion';
                }
            } else if (@$this->config->usecompletion == 2) {
                if (is_dir($CFG->dirroot.'/mod/learningtimeckeck')) {
                    include_once($CFG->dirroot.'/mod/learningtimecheck/xlib.php');
                    if (learningtimecheck_course_has_ltc_tracking($this->course->id)) {
                        $this->marks = learningtimecheck_get_course_marks($this->course->id, $user->id);
                    }
                    $coursetpl->completionon = 'marks';
                }
            }

            if ($coursetpl->completionon != 'off') {
                $coursetpl->complete = 'assumedcomplete';
            } else {
                $coursetpl->complete = '';
            }

            if ($thiscontext->get_level_name() == get_string('activitymodule')) {
                // Display nothing.
                return $this->content;
            }

            $sectionnums = array();
            foreach ($sections as $section) {
                $sectionnums[] = $section->section;
            }
            foreach ($sections as $section) {
                if (($this->course->format == 'flexsections')) {
                    $params = array('courseid' => $this->course->id,
                                    'sectionid' => $section->id,
                                    'name' => 'parent');
                    $section->parent = 0 + $DB->get_field('course_format_options', 'value', $params);
                    // Ignore non parent sections. They will be processed as subs.
                    if ($section->parent) {
                        continue;
                    }
                }
                $sectiontpl = $this->make_section($section, $coursetpl);
                if (!empty($sectiontpl)) {
                    $coursetpl->sections[] = $sectiontpl;
                }
            }

            if ($coursetpl->complete == 'assumedcomplete') {
                $coursetpl->complete = 'empty';
            }

            $template->courses[] = $coursetpl;
        }

        $template->config = $this->config;
        $renderer = $this->page->get_renderer('block_multicourse_navigation', 'nav');
        // print_object($template);
        $this->content->text = $renderer->render_nav($template);

        return $this->content;
    }

    /**
     *
     *
     *
     */
    protected function make_section(&$section, &$coursetpl) {
        static $deepnesscontrol = 0;
        static $maxdeepness = 10;

        @$deepnesscontrol++;
        if ($deepnesscontrol > $maxdeepness) {
            throw new coding_exception("MakeSection Went too deep");
        }

        $i = $section->section;

        if ($this->course->format != 'flexsections') {
            if ($i > @$this->course->numsections) {
                return;
            }
        }

        if (($i > 0) && !$section->uservisible) {
            return;
        }

        if (!empty($section->name)) {
            $name = format_string($section->name, true, array('context' => $this->context));
        } else {
            $summary = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php', $this->context->id, 'course',
                'section', $section->id);
            $summary = format_text($summary, $section->summaryformat, array('para' => false, 'context' => $this->context));
            $name = $this->format->get_section_name($section);
        }

        $sectiontpl = new stdClass();
        $sectiontpl->number = $i;
        $sectiontpl->sectionid = $section->id;
        $sectiontpl->sectionname = shorten_text($name, 40);
        $sectiontpl->sectionfullname = $name;
        $sectiontpl->indent = str_repeat('&nbsp;&nbsp;', $deepnesscontrol);
        $sectionsection = $section->section;
        if ($sectionsection) {
            if ($this->config->showsectionlinks == 1) {
                $sectiontpl->url = new moodle_url('/course/view.php', array('id' => $this->course->id), 'section-'.$sectionsection);
            } else {
                $sectiontpl->url = $this->format->get_view_url($sectionsection);
            }
        }

        if ($coursetpl->completionon != 'off') {
            $sectiontpl->complete = 'assumedcomplete';
        } else {
            $sectiontpl->complete = '';
        }

        if (empty($this->userstates['section'][$section->id])) {
            $state = 'expanded';
        } else {
            $state = 'collapsed';
            $sectiontpl->initial = 'style="display:none; visibility: hidden"';
        }

        // Make Handle to expand/collapse.
        /*
        if ($section->id == $this->currentsectionid) {
            // Display the menu.
            $state = 'expanded';
        } else {
            // Go to link.
            $state = 'collapsed';
        }
        */
        $sectiontpl->handle = '';
        if (!empty($this->config->showmodules)) {
            $sectiontpl->handle = html_writer::img($this->hicons[$state.'nosubs'], get_string($state, 'block_multicourse_navigation'));
        }

        $sectiontpl->modules = array();

        if (!empty($this->modinfo->sections[$i])) {

            foreach ($this->modinfo->sections[$i] as $modnumber) {

                $module = $this->modinfo->cms[$modnumber];

                if (preg_match('/label$/', $module->modname)) {
                    continue;
                }

                if (! $module->uservisible) {
                    continue;
                }

                $modtpl = new stdClass();

                $modtpl->name = format_string($module->name, true, array('context' => $this->context));
                $modtpl->url = $module->url;
                $modtpl->icon = html_writer::img($module->get_icon_url(), '', null);

                if ($coursetpl->completionon != 'off') {

                    $hascompletion = false;
                    if ($coursetpl->completionon == 'completion') {
                        $hascompletion = $this->completioninfo->is_enabled($module);
                        if ($hascompletion) {
                            $modtpl->complete = 'incomplete';
                        }

                        $completiondata = $this->completioninfo->get_data($module, true);
                        $hascompleted = 0 + in_array($completiondata->completionstate, $this->completionok);
                    } else {
                        if (in_array($mod->cm->id, $this->marks)) {
                            $hascompletion = true;
                            $modtpl->complete = 'incomplete';
                            $hascompleted = $this->marks[$mod->cm->id];
                        } else {
                            // Skip module as not in mandatory part.
                            continue;
                        }
                    }

                    if ($hascompletion) {
                        if ($hascompleted) {
                            if ($sectiontpl->complete == 'assumedcomplete') {
                                $sectiontpl->complete = 'complete';
                            }
                            if ($coursetpl->complete == 'assumedcomplete') {
                                $coursetpl->complete = 'complete';
                            }
                            $modtpl->complete = 'complete';
                        } else {
                            if ($sectiontpl->complete == 'assumedcomplete') {
                                $sectiontpl->complete = 'incomplete';
                            }
                            if ($coursetpl->complete == 'assumedcomplete') {
                                $coursetpl->complete = 'incomplete';
                            }
                        }
                    }
                }
                if (!empty($this->config->showmodules)) {
                    $sectiontpl->modules[] = $modtpl;
                }
            }
            if (!empty($this->config->showmodules)) {
                $sectiontpl->hasmodules = (count($sectiontpl->modules) > 0);
            }
        }

        $sectiontpl->hassubs = 0;
        $sectiontpl->leafclass = 'is-leaf';
        if ($this->course->format == 'flexsections' && $section->section > 0) {

            $subs = $this->format->get_subsections($section);
            if (!empty($subs)) {
                $sectiontpl->hassubs = 1;
                $sectiontpl->leafclass = '';
                foreach ($subs as $sub) {
                    $subtpl = $this->make_section($sub, $coursetpl);

                    // Report completion on the current level.
                    if ($subtpl->complete == 'empty') {
                        // No effect on current section state.
                    } else if ($subtpl->complete != 'complete') {
                        if ($sectiontpl->complete == 'assumedcomplete') {
                                $sectiontpl->complete = 'incomplete';
                        } else {
                            $sectiontpl->complete = 'incomplete';
                        }
                    } else {
                        if ($sectiontpl->complete != 'incomplete') {
                            $sectiontpl->complete = 'complete';
                        }
                    }

                    $sectiontpl->sections[] = $subtpl;
                }

                // If has subs, overrides handle icon to reflect complete content.
                $sectiontpl->handle = html_writer::img($this->hicons[$state], get_string($state, 'block_multicourse_navigation'));

            }
        }

        $deepnesscontrol--;

        if ($sectiontpl->complete == 'assumedcomplete') {
            $sectiontpl->complete = 'empty';
        }

        return $sectiontpl;
    }

    public function get_required_javascript() {
        global $PAGE, $COURSE;

        $PAGE->requires->js_call_amd('block_multicourse_navigation/collapse_control', 'init', array('courseid' => $COURSE->id));
    }

}

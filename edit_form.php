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

/**
 * Extends the block instance configuration
 */
class block_multicourse_navigation_edit_form extends block_edit_form {

    /**
     * Defines fields to add to the settings form
     *
     * @param moodle_form $mform
     */
    protected function specific_definition($mform) {
        global $COURSE;

        $config = get_config('block_multicourse_navigation');

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'core_block'));

        $mform->addElement('text', 'config_blocktitle', get_string('config_blocktitle', 'block_multicourse_navigation'));
        $mform->setDefault('config_blocktitle', '');
        $mform->setType('config_blocktitle', PARAM_MULTILANG);

        $mform->addHelpButton('config_blocktitle', 'config_blocktitle', 'block_multicourse_navigation');

        $completionoptions = array(0 => get_string('nocompletion', 'block_multicourse_navigation'),
                                   1 => get_string('completion', 'block_multicourse_navigation'),
                                   2 => get_string('learningtimecheck', 'block_multicourse_navigation'));
        $title = get_string('config_usecompletion', 'block_multicourse_navigation');
        $mform->addElement('select', 'config_usecompletion', $title, $completionoptions);
        $mform->setDefault('config_usecompletion', 1);

        $contractoptions = array(LTC_OPTIONAL_YES => get_string('allmarks', 'block_multicourse_navigation'),
                                   LTC_OPTIONAL_NO => get_string('mandatoryonly', 'block_multicourse_navigation'));
        $title = get_string('config_ltccontract', 'block_multicourse_navigation');
        $mform->addElement('select', 'config_ltccontract', $title, $contractoptions);
        $mform->disabledIf('config_ltccontract', 'config_usecompletion', 'neq', 2);

        // Adds a list of course ids to track in order.
        $title = get_string('config_showmodules', 'block_multicourse_navigation');
        $mform->addElement('advcheckbox', 'config_showmodules', $title);
        $mform->setDefault('config_showmodules', $config->defaultshowmodules);

        // Adds a list of course ids to track in order.
        $title = get_string('config_showsectionlinks', 'block_multicourse_navigation');
        $options = array(0 => get_string('no'),
                         1 => get_string('anchortosection', 'block_multicourse_navigation'),
                         2 => get_string('onesectionview', 'block_multicourse_navigation'));
        $mform->addElement('select', 'config_showsectionlinks', $title, $options);
        $mform->setDefault('config_showsectionlinks', $config->defaultshowsectionlinks);

        // Adds a list of course ids to track in order.
        $title = get_string('config_trackcourses', 'block_multicourse_navigation');
        $mform->addElement('text', 'config_trackcourses', $title);
        $mform->setDefault('config_trackcourses', $COURSE->id);
        $mform->setType('config_trackcourses', PARAM_TEXT);
        $mform->addHelpButton('config_trackcourses', 'config_trackcourses', 'block_multicourse_navigation');

    }
}

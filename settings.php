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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Option: 
    $name = 'block_multicourse_navigation/defaultshowmodules';
    $title = get_string('config_defaultshowmodules', 'block_multicourse_navigation');
    $description = get_string('config_defaultshowmodules_desc', 'block_multicourse_navigation');
    $default = 1;
    $settings->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    // Option: show sectionlinks.
    $name = 'block_multicourse_navigation/defaultshowsectionlinks';
    $title = get_string('config_defaultshowsectionlinks', 'block_multicourse_navigation');
    $description = get_string('config_defaultshowsectionlinks_desc', 'block_multicourse_navigation');
    $default = 1;
    $options = array(0 => get_string('no'),
                     1 => get_string('anchortosection', 'block_multicourse_navigation'),
                     2 => get_string('onesectionview', 'block_multicourse_navigation'));
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $options));

    // Option: Show all tabs open.
    $name = 'block_multicourse_navigation/initialcollapsed';
    $title = get_string('config_initialcollapsed', 'block_multicourse_navigation');
    $description = get_string('config_initialcollapsed_desc', 'block_multicourse_navigation');
    $default = 1;
    $settings->add(new admin_setting_configcheckbox($name, $title, $description, $default));

    // Option: Ignore the General section 0 in display.
    $name = 'block_multicourse_navigation/defaultignoremainsection';
    $title = get_string('config_defaultignoremainsection', 'block_multicourse_navigation');
    $description = get_string('config_defaultignoremainsection_desc', 'block_multicourse_navigation');
    $default = 1;
    $settings->add(new admin_setting_configcheckbox($name, $title, $description, $default));

}

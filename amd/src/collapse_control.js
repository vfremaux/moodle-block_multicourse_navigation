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
 * Javascript controller for controlling the sections.
 *
 * @module     block_multicourse_navigation/collapse_control
 * @package    block_multicourse_navigation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// jshint unused: true, undef:true
define(['jquery', 'core/config', 'core/log'], function($, config, log) {

    var currentcourseid;

    /**
     * SectionControl class.
     *
     * @param {String} selector The selector for the page region containing the actions panel.
     */
    return {

        init: function(args) {

            // Attach togglestate handler to all handles in page.
            $('.block_multicourse_navigation .node-course .handle').on('click', this.togglecoursestate);
            log.debug('Block course modulenavigation course control initialized');

            $('.block_multicourse_navigation .node-section .handle').on('click', this.togglesectionstate);
            log.debug('Block course modulenavigation section control initialized');

            currentcourseid = args;
        },

        togglecoursestate: function(e) {

            e.stopPropagation();
            e.preventDefault();
            var that = $(this);

            regex = /node-course-([0-9]+)-block-([0-9]+)/;
            log.debug('Courseid ' + currentcourseid);
            log.debug('Elm ' + that.parent().attr('id'));
            matchs = regex.exec(that.parent().attr('id'));
            if (!matchs) {
                return;
            }
            courseid = parseInt(matchs[1]);
            blockid = parseInt(matchs[2]);

            log.debug('Working for block ' + blockid + ' in course ' + courseid);

            url = config.wwwroot + '/blocks/course_modulenavigation/ajax/stateregister.php?';
            url += 'id=' + currentcourseid;
            url += '&item=course';
            url += '&itemid=' + courseid;
            url += '&blockid=' + blockid;

            handlesrc = $('#node-course-' + courseid + '-block-' + blockid + ' .handle > img').attr('src');

            if ($('#course-content-' + courseid).css('visibility') === 'visible') {
                $('#course-content-' + courseid).css('visibility', 'hidden');
                $('#course-content-' + courseid).css('display', 'none');
                handlesrc = handlesrc.replace('expanded', 'collapsed');
                $('#node-course-' + courseid + '-block-' + blockid + ' .handle > img').attr('src', handlesrc);
                hide = 1;
            } else {
                $('#course-content-' + courseid).css('visibility', 'visible');
                $('#course-content-' + courseid).css('display', 'block');
                handlesrc = handlesrc.replace('collapsed', 'expanded');
                $('#node-course-' + courseid + '-block-' + blockid + ' .handle > img').attr('src', handlesrc);
                hide = 0;
            }

            url += '&hide=' + hide;

            $.get(url, function(data) {
            });

            return false;
        },

        togglesectionstate: function(e) {

            e.stopPropagation();
            e.preventDefault();
            var that = $(this);

            regex = /node-section-([0-9]+)-block-([0-9]+)/;
            log.debug('Courseid ' + currentcourseid);
            log.debug('Elm ' + that.parent().attr('id'));
            matchs = regex.exec(that.parent().attr('id'));
            if (!matchs) {
                return;
            }
            sectionid = parseInt(matchs[1]);
            blockid = parseInt(matchs[2]);

            log.debug('Working for block ' + blockid + ' and section ' + sectionid);

            url = config.wwwroot + '/blocks/course_modulenavigation/ajax/stateregister.php?';
            url += 'id=' + currentcourseid;
            url += '&item=section';
            url += '&itemid=' + sectionid;
            url += '&blockid=' + blockid;

            handlesrc = $('#node-section-' + sectionid + '-block-' + blockid + ' .handle > img').attr('src');

            if ($('#section-content-' + sectionid).css('visibility') === 'visible') {
                $('#section-content-' + sectionid).css('visibility', 'hidden');
                $('#section-content-' + sectionid).css('display', 'none');
                handlesrc = handlesrc.replace('expanded', 'collapsed');
                $('#node-section-' + sectionid + '-block-' + blockid + ' .handle > img').attr('src', handlesrc);
                hide = 1;
            } else {
                $('#section-content-' + sectionid).css('visibility', 'visible');
                $('#section-content-' + sectionid).css('display', 'block');
                handlesrc = handlesrc.replace('collapsed', 'expanded');
                $('#node-section-' + sectionid + '-block-' + blockid + ' .handle > img').attr('src', handlesrc);
                hide = 0;
            }

            url += '&hide=' + hide;

            $.get(url, function(data) {
            });

            return false;
        }
    }

});

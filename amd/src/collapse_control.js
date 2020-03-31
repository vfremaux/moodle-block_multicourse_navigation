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

    var multicoursenavigation = {

        currentcourseid: 0,

        init: function(args) {

            // Attach togglestate handler to all handles in page.
            $('.block_multicourse_navigation .node-course .handle').on('click', this.togglecoursestate);
            log.debug('Block course modulenavigation course control initialized');

            $('.block_multicourse_navigation .node-section .handle').on('click', this.togglesectionstate);
            log.debug('Block course modulenavigation section control initialized');

            // Attach global controls to the block
            $('.multicourse-controls').on('click', this.processglobal);

            multicoursenavigation.currentcourseid = args;
        },

        processglobal: function(e) {
            e.stopPropagation();
            e.preventDefault();
            var that = $(this);

            var regex = /multicourse-([a-z]+)-(\d+)/;
            var matchs = regex.exec(that.attr('id'));
            var what = matchs[1];
            var blockid = matchs[2];

            var url = config.wwwroot + '/blocks/multicourse_navigation/ajax/service.php?';
            url += 'blockid=' + blockid;
            url += '&what=' + what;

            switch (what) {
                case 'collapseall':
                    $('.section-content.block-' + blockid).css('display', 'none');
                    $('.section-content.block-' + blockid).css('visibility', 'hidden');
                    break;

                case 'expandall':
                    $('.section-content.block-' + blockid).css('display', 'block');
                    $('.section-content.block-' + blockid).css('visibility', 'visible');
                    break;

                case 'showmodules':
                    $('.section-content.block-' + blockid + ' .activities').css('display', 'block');
                    $('.section-content.block-' + blockid + ' .activities').css('visibility', 'visible');
                    break;

                case 'hidemodules':
                    $('.section-content.block-' + blockid + ' .activities').css('display', 'none');
                    $('.section-content.block-' + blockid + ' .activities').css('visibility', 'hidden');
                    break;
            }

            $.get(url);
        },

        togglecoursestate: function(e) {

            e.stopPropagation();
            e.preventDefault();
            var that = $(this);

            var regex = /node-course-([0-9]+)-block-([0-9]+)/;
            log.debug('Courseid ' + multicoursenavigation.currentcourseid);
            log.debug('Elm ' + that.parent().attr('id'));
            var matchs = regex.exec(that.parent().attr('id'));
            if (!matchs) {
                return;
            }
            var courseid = parseInt(matchs[1]);
            var blockid = parseInt(matchs[2]);
            var hide;

            log.debug('Working for block ' + blockid + ' in course ' + courseid);

            var url = config.wwwroot + '/blocks/multicourse_navigation/ajax/stateregister.php?';
            url += 'id=' + multicoursenavigation.currentcourseid;
            url += '&item=course';
            url += '&itemid=' + courseid;
            url += '&blockid=' + blockid;

            var handlesrc = $('#node-course-' + courseid + '-block-' + blockid + ' .handle > img').attr('src');

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

            $.get(url, function() {
            });

            return false;
        },

        togglesectionstate: function(e) {

            e.stopPropagation();
            e.preventDefault();
            var that = $(this);
            var hide;

            var regex = /node-block-([0-9]+)-section-([0-9]+)/;
            log.debug('Courseid ' + multicoursenavigation.currentcourseid);
            log.debug('Elm ' + that.parent().attr('id'));
            var matchs = regex.exec(that.parent().attr('id'));
            if (!matchs) {
                return;
            }
            var blockid = parseInt(matchs[1]);
            var sectionid = parseInt(matchs[2]);

            log.debug('Working for block ' + blockid + ' and section ' + sectionid);

            var url = config.wwwroot + '/blocks/multicourse_navigation/ajax/stateregister.php?';
            url += 'id=' + multicoursenavigation.currentcourseid;
            url += '&item=section';
            url += '&itemid=' + sectionid;
            url += '&blockid=' + blockid;

            var handlesrc = $('#node-block-' + blockid + '-section-' + sectionid + ' .handle > img').attr('src');

            if ($('#section-content-' + sectionid).css('visibility') === 'visible') {
                $('#section-content-' + sectionid).css('visibility', 'hidden');
                $('#section-content-' + sectionid).css('display', 'none');
                handlesrc = handlesrc.replace('expanded', 'collapsed');
                $('#node-block-' + blockid + '-section-' + sectionid + ' .handle > img').attr('src', handlesrc);
                hide = 1;
            } else {
                $('#section-content-' + sectionid).css('visibility', 'visible');
                $('#section-content-' + sectionid).css('display', 'block');
                handlesrc = handlesrc.replace('collapsed', 'expanded');
                $('#node-block-' + blockid + '-section-' + sectionid + ' .handle > img').attr('src', handlesrc);
                hide = 0;
            }

            url += '&hide=' + hide;

            $.get(url, function() {
            });

            return false;
        }
    };

    return multicoursenavigation;

});

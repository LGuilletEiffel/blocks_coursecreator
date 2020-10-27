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
 * Settings file for Block Course creator
 *
 * @package   block_coursecreator
 * @author     Laurent GUILLET <laurent.guillet@u-pem.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $DB;

$listcategories = $DB->get_records('course_categories');

$readablelistcategories = array();

foreach ($listcategories as $category) {

    $readablelistcategories[$category->id] = $category->name;
}

$settings->add(new admin_setting_heading(
                'headerconfig',
                get_string('headerconfig', 'block_coursecreator'),
                get_string('descconfig', 'block_coursecreator')
));

$settings->add(new admin_setting_confightmleditor(
                'coursecreator/introtextsettings',
                get_string('introtextsettings', 'block_coursecreator'),
                null,
                null
));

$settings->add(new admin_setting_configselect(
                'coursecreator/defaultdestinationcategorysettings',
                get_string('defaultdestinationcategory', 'block_coursecreator'),
                null,
                null,
                $readablelistcategories
));

$settings->add(new admin_setting_configmultiselect(
                'coursecreator/destinationcategoriessettings',
                get_string('destinationcategories', 'block_coursecreator'),
                null,
                null,
                $readablelistcategories
));

$settings->add(new admin_setting_configmultiselect(
                'coursecreator/origincategoriessettings',
                get_string('origincategories', 'block_coursecreator'),
                null,
                null,
                $readablelistcategories
));

$settings->add(new admin_setting_configtext(
                'coursecreator/suffixcohort',
                get_string('suffixcohort', 'block_coursecreator'),
                null,
                null
));

$settings->add(new admin_setting_configtext(
                'coursecreator/mailrecipients',
                get_string('mailrecipients', 'block_coursecreator'),
                null,
                null
));

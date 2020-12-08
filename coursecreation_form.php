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
 * Course creation Form file
 *
 * @package    block_coursecreator
 * @author     Laurent GUILLET <laurent.guillet@u-pem.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Moodleform is defined in formslib.php.
require_once("$CFG->libdir/formslib.php");

class coursecreation_form extends moodleform {

    // Add elements to form.
    public function definition() {
        global $DB, $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('html', get_config('coursecreator', 'introtextsettings'));

        $mform->addElement('text', 'newcoursename', get_string('newcoursename', 'block_coursecreator'),
                'maxlength="200"');
        $mform->setType('newcoursename', PARAM_TEXT);
        $mform->addRule('newcoursename', get_string('requiredfieldform', 'block_coursecreator'),
                'required');

        $mform->addElement('text', 'apogeecode', get_string('apogeecode', 'block_coursecreator'));
        $mform->setType('apogeecode', PARAM_TEXT);
        $mform->addRule('apogeecode', get_string('requiredfieldform', 'block_coursecreator'),
                'required');

        $destinationcategoriesid = get_config('coursecreator', 'destinationcategoriessettings');
        $tabdestinationcategoriesid = explode(',', $destinationcategoriesid);

        $destinationcategories = array();

        foreach ($tabdestinationcategoriesid as $destinationcategoryid) {

            $destinationcategories[$destinationcategoryid] = $DB->get_record('course_categories',
                            array('id' => $destinationcategoryid))->name;
        }

        $mform->addElement('select', 'destinationcategory',
                get_string('destinationcategory', 'block_coursecreator'),
                $destinationcategories);
        $mform->setDefault('destinationcategory', get_config('coursecreator',
                        'defaultdestinationcategorysettings'));

        $mform->addElement('html', get_string('importtext', 'block_coursecreator'));

        // Récupérer tous les cours où il est enseignant dans les catégories autorisées.

        $parentcategoriesid = get_config('coursecreator', 'origincategoriessettings');
        $tabparentcategoriesid = explode(',', $parentcategoriesid);

        $coursesteachedincategories = $this->getcoursesteachedincategories($tabparentcategoriesid);

        $mform->addElement('select', 'coursechoice',
                get_string('coursechoice', 'block_coursecreator'), $coursesteachedincategories);

        $mform->addElement('html', get_string('addstudents', 'block_coursecreator'));

        $defaultdestinationcategorycontextid = $DB->get_record('context',
                        array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => get_config('coursecreator',
                                    'defaultdestinationcategorysettings')))->id;

        $listcohorts = $DB->get_records('cohort', array('contextid' => $defaultdestinationcategorycontextid, 'visible' => 1));

        $listcohortsform = array();
        $listcohortsform[0] = get_string('nocohort', 'block_coursecreator');

        foreach ($listcohorts as $cohort) {

            $listcohortsform[$cohort->id] = $cohort->name;
        }

        $optionscohort = array(
            'multiple' => true,
            'ajax' => 'tool_lp/form-cohort-selector',
            'data-contextid' => $defaultdestinationcategorycontextid,
        );
        $mform->addElement('autocomplete', 'cohortchoice', get_string('cohortchoice', 'block_coursecreator'), array(), $optionscohort);

//        $mform->addElement('text', 'apogeecodestudent',
//                get_string('apogeecodestudent', 'block_coursecreator'));
//        $mform->setType('apogeecodestudent', PARAM_TEXT);

        $optionsuser = array(
            'multiple' => true,
            'ajax' => 'tool_lp/form-user-selector'
        );
        $mform->addElement('autocomplete', 'namestudent', get_string('selectstudent', 'block_coursecreator'), array(), $optionsuser);

        $mform->addElement('editor', 'commentteacher', get_string('commentteacher', 'block_coursecreator'));

        $this->add_action_buttons(true, get_string('validateform', 'block_coursecreator'));
    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        return array();
    }

    public function getcoursesteachedincategories($listcategories) {

        global $DB, $USER;

        $listcourses = array();
        $listcourses[0] = get_string('nocopycourse', 'block_coursecreator');

        $teacherroleid = $DB->get_record('role', array('shortname' => 'editingteacher'))->id;

        $listteacherroles = $DB->get_records('role_assignments', array('roleid' => $teacherroleid, 'userid' => $USER->id));

        foreach ($listteacherroles as $teacherrole) {

            $courseid = $DB->get_record('context', array('id' => $teacherrole->contextid))->instanceid;

            $course = $DB->get_record('course', array('id' => $courseid));

            $coursecategory = $course->category;

            foreach ($listcategories as $category) {

                if ($category == $coursecategory) {

                    $listcourses[$courseid] = $course->shortname . ' ' . $course->fullname;
                    break;
                }
            }
        }

        return $listcourses;
    }
}

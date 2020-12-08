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
 * Instantiate the course creation Form file
 *
 * @package    block_coursecreator
 * @author     Laurent GUILLET <laurent.guillet@u-pem.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/coursecreator/coursecreation_form.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/ui/import_extensions.php');

$systemcontext = context_system::instance();
require_capability('block/coursecreator:createcourse', $systemcontext);

$PAGE->set_url('/blocks/coursecreator/coursecreation.php');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());

$title = get_string('pluginname', 'block_coursecreator');
$PAGE->set_title($title);
$PAGE->set_heading($title);

require_login();

// Instantiate simplehtml_form.
$mform = new coursecreation_form();

$redirecturlhome = new moodle_url('/my');

// Form processing and displaying is done here.
if ($mform->is_cancelled()) {

    redirect($redirecturlhome);
} else if ($fromform = $mform->get_data()) {
    // In this case you process validated data. $mform->get_data() returns data posted in form.

    $shortname = block_coursecreator_tryshortname($fromform->apogeecode, 0);

    $coursedata = new stdClass;
    $coursedata->fullname = $fromform->newcoursename;
    $coursedata->shortname = $shortname;
    $coursedata->category = $fromform->destinationcategory;

    $newcourse = create_course($coursedata);
    $redirecturlcourse = new moodle_url("/course/view.php?id=$newcourse->id");
    $newcontext = context_course::instance($newcourse->id, MUST_EXIST);

    // Enrol current user in the new course, with editing teacher role.

    $enrol = $DB->get_record('enrol', array('enrol' => 'manual', 'courseid' => $newcontext->instanceid));
    $enrolplugin = new enrol_manual_plugin();
    $teacherroleid = $DB->get_record('role', array('shortname' => 'editingteacher'))->id;
    $enrolplugin->enrol_user($enrol, $USER->id, $teacherroleid);

    $countcohorts = 0;
    $listcorrectcohortusers = array();
    $typecohort = 0;
    $cohortid = 0;
    $studentid = 0;

    if ($fromform->coursechoice != 0) {

        copycourse($fromform->coursechoice, $newcourse->id);
    }


    foreach ($fromform->cohortchoice as $cohortchoice) {

        enrolcohort($cohortchoice, $newcourse->id);
    }

    foreach ($fromform->namestudent as $studentid) {

        // Chercher les cohortes où il est inscrit.
        // Vérifier qu'il n'y en a qu'une dans la catégorie autorisée puis enrolcohort.

        $listcohortsuser = $DB->get_records('cohort_members', array('userid' => $studentid));

        $contextdestinationcategoryid = $DB->get_record('context',
                        array('contextlevel' => CONTEXT_COURSECAT,
                            'instanceid' => get_config('coursecreator', 'defaultdestinationcategorysettings')))->id;

        foreach ($listcohortsuser as $cohortuser) {

            if ($DB->record_exists('cohort', array('id' => $cohortuser->cohortid, 'contextid' => $contextdestinationcategoryid))) {

                $countcohorts++;

                if ($countcohorts == 1) {

                    $cohortid = $DB->get_record('cohort',
                                    array('id' => $cohortuser->cohortid, 'contextid' => $contextdestinationcategoryid))->id;
                }

                $listcorrectcohortusers[] = $DB->get_record('cohort',
                        array('id' => $cohortuser->cohortid, 'contextid' => $contextdestinationcategoryid));
            }
        }

        if ($countcohorts == 1) {

            enrolcohort($cohortid, $newcourse->id);
        }
    }

// Reliquat de la troisième méthode d'inscription des cohortes. Conservés ici pour s'y on veut la réadapter pour s'en servir.
//    } else if ($fromform->apogeecodestudent != "") {
//
//        // Vérifier si une cohorte a cet idnumber (en rajoutant le suffixe) dans la catégorie autorisée puis enrolcohort.
//
//        $idnumber = $fromform->apogeecodestudent . get_config('coursecreator', 'suffixcohort');
//        $contextdestinationcategoryid = $DB->get_record('context',
//                        array('contextlevel' => CONTEXT_COURSECAT,
//                            'instanceid' => get_config('coursecreator', 'defaultdestinationcategorysettings')))->id;
//
//        if ($DB->record_exists('cohort', array('idnumber' => $idnumber, 'contextid' => $contextdestinationcategoryid))) {
//
//            $cohortid = $DB->get_record('cohort', array('idnumber' => $idnumber, 'contextid' => $contextdestinationcategoryid))->id;
//
//            enrolcohort($cohortid, $newcourse->id);
//        } else if ($DB->record_exists('cohort', array('idnumber' => $fromform->apogeecodestudent,
//                    'contextid' => $contextdestinationcategoryid))) {
//
//            $cohortid = $DB->get_record('cohort', array('idnumber' => $fromform->apogeecodestudent,
//                        'contextid' => $contextdestinationcategoryid))->id;
//
//            enrolcohort($cohortid, $newcourse->id);
//        } else {
//
//            $cohortid = 0;
//        }
//
//        $typecohort = 2;

    sendmail($typecohort, $fromform->coursechoice, $coursedata->fullname, $coursedata->shortname,
            $newcourse->id, $fromform->commentteacher, $fromform->namestudent);

    redirect($redirecturlcourse);
} else {

    echo $OUTPUT->header();
    $mform->display();
}

echo $OUTPUT->footer();

function block_coursecreator_tryshortname($coursename, $i) {

    global $DB;

    $newshortname = $coursename;

    if ($i) {

        $newshortname .= "_$i";
    }

    $already = $DB->record_exists('course', array('shortname' => $newshortname));

    if ($already) {

        return block_coursecreator_tryshortname($coursename, $i + 1);
    } else {

        return $newshortname;
    }
}

function copycourse($oldcourseid, $newcourseid) {

    global $USER;

    deleteforum($newcourseid);

    $backup = new backup_controller(backup::TYPE_1COURSE, $oldcourseid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
    $backup->execute_plan();

    $rc = new restore_controller($backup->get_backupid(), $newcourseid,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_DELETING);

    $rc->execute_precheck();

    $rc->get_precheck_results();

    // Execute the restore.
    $rc->execute_plan();
}

function deleteforum($courseid) {

    global $DB;

    $forummoduleid = $DB->get_record('modules', array('name' => 'forum'))->id;

    if ($DB->record_exists('course_modules', array('course' => $courseid, 'module' => $forummoduleid))) {

        $forumtodelete = $DB->get_record('course_modules', array('course' => $courseid, 'module' => $forummoduleid));

        delete_mod_from_section($forumtodelete->id, $forumtodelete->section);
    }
}

function enrolcohort($cohortid, $courseid) {

    global $DB;

    $course = $DB->get_record('course', array('id' => $courseid));

    $studentroleid = $DB->get_record('role', array('shortname' => 'student'))->id;

    $cohortplugin = enrol_get_plugin('cohort');
    $cohortplugin->add_instance($course, array('customint1' => $cohortid, 'roleid' => $studentroleid,
        'customint2' => null));

    $trace = new null_progress_trace();
    enrol_cohort_sync($trace, $courseid);
    $trace->finished();
}

function sendmail($typecohort, $origincourseid, $coursefullname, $courseshortname, $newcourseid,
        $countcohorts, $listcorrectcohortusers, $commentteacher, $cohortid, $apogeecode, $studentid) {

    // A modifier maintenant qu'on peut inscrire plusieurs cohortes.

    global $USER, $DB, $CFG;

    if ($typecohort == 3) {

        $student = $DB->get_record('user', array('id' => $studentid));
        $studentname = $student->firstname . " " . $student->lastname;
    }

    $to = get_config('coursecreator', 'mailrecipients');

    $stringsubjectparam = new stdClass();
    $stringsubjectparam->teacher = "$USER->firstname $USER->lastname";
    $stringsubjectparam->coursename = $coursefullname;

    $subject = utf8_decode(get_string('mailsubject', 'block_coursecreator', $stringsubjectparam));

    $stringstartparam = new stdClass();
    $stringstartparam->teacher = "$USER->firstname $USER->lastname";
    $stringstartparam->coursename = $coursefullname;
    $stringstartparam->courseshortname = $courseshortname;

    $message = get_string('messagestart', 'block_coursecreator', $stringstartparam);

    if ($origincourseid != 0) {

        $origincourse = $DB->get_record('course', array('id' => $origincourseid));

        $stringcourseparam = new stdClass();
        $stringcourseparam->coursename = $origincourse->fullname;
        $stringcourseparam->courseurl = $CFG->wwwroot . '/course/view?id=' . $origincourseid;

        $message .= "\n" . get_string('messagecourse', 'block_coursecreator', $stringcourseparam);
    }

    // Cas des cohortes.
    if ($typecohort == 0) {

        // Ne rien faire.
        $message .= "";
    } else if ($typecohort == 1) {
        // Cas 1 : Cohorte inscrite via la liste.

        $cohort = $DB->get_record('cohort', array('id' => $cohortid));

        $stringcohortparam = new stdClass();
        $stringcohortparam->name = $cohort->name;

        $message .= "\n" . get_string('messagecohortlist', 'block_coursecreator', $stringcohortparam);
    } else if ($typecohort == 2 && $cohortid != 0) {
        // Cas 2 : Cohorte inscrite via le code Apogée.

        $cohort = $DB->get_record('cohort', array('id' => $cohortid));

        $stringcohortparam = new stdClass();
        $stringcohortparam->name = $cohort->name;
        $stringcohortparam->apogeecode = $apogeecode;
        $message .= "\n" . get_string('messagecohortapogee', 'block_coursecreator', $stringcohortparam);
    } else if ($typecohort == 2 && $cohortid == 0) {
        // Cas 3 : Cohorte non trouvée malgré le code Apogée.

        $stringcohortparam = new stdClass();
        $stringcohortparam->apogeecode = $apogeecode;
        $message .= "\n" . get_string('messagenocohortapogee', 'block_coursecreator', $stringcohortparam);
    } else if ($typecohort == 3 && $countcohorts == 1) {
        // Cas 4 : Cohorte inscrite grâce au nom de l'étudiant.

        $cohort = $DB->get_record('cohort', array('id' => $cohortid));

        $stringcohortparam = new stdClass();
        $stringcohortparam->name = $cohort->name;
        $stringcohortparam->studentname = $studentname;
        $message .= "\n" . get_string('messagecohortstudent', 'block_coursecreator', $stringcohortparam);
    } else if ($typecohort == 3 && $countcohorts == 0) {
        // Cas 5 : Aucun cohorte trouvée avec l'étudiant.

        $stringcohortparam = new stdClass();
        $stringcohortparam->studentname = $studentname;
        $message .= "\n" . get_string('messagenocohortstudent', 'block_coursecreator', $stringcohortparam);
    } else if ($typecohort == 3 && $countcohorts > 1) {
        // Cas 6 : Plusieurs cohortes trouvées avec l'étudiant.

        $stringlistcohort = "";

        foreach ($listcorrectcohortusers as $correctcohortuser) {

            $stringlistcohort .= $correctcohortuser->name . "\n";
        }

        $stringcohortparam = new stdClass();
        $stringcohortparam->studentname = $studentname;
        $stringcohortparam->listcohorts = $stringlistcohort;
        $message .= "\n" . get_string('messagelistcohortstudent', 'block_coursecreator', $stringcohortparam);
    } else {
        // Ne devrait jamais arriver.

        $message .= "\n" . get_string('messageerror', 'block_coursecreator');
    }

    if ($commentteacher != "") {

        $stringcommentparam = new stdClass();
        $stringcommentparam->commentteacher = html_to_text($commentteacher['text']);
        $message .= "\n" . get_string('messagecommentteacher', 'block_coursecreator', $stringcommentparam);
    }

    $stringcourseparam = new stdClass();
    $stringcourseparam->courseurl = $CFG->wwwroot . '/course/view.php?id=' . $newcourseid;

    $message .= "\n" . get_string('messageending', 'block_coursecreator', $stringcourseparam);

    mail($to, $subject, $message);
}

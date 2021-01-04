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
 * Strings for component 'block_coursecreator', language 'en'
 *
 * @package   block_coursecreator
 * @author     Laurent GUILLET <laurent.guillet@u-pem.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['coursecreator'] = 'Créateur de cours';
$string['coursecreator:myaddinstance'] = 'Ajouter un nouveau bloc Créateur de cours sur le tableau de bord';
$string['coursecreator:addinstance'] = 'Ajouter un nouveau bloc Créateur de cours';
$string['coursecreator:createcourse'] = 'Peut créer un cours via le bloc';
$string['pluginname'] = 'Bloc Créateur de cours';
$string['privacy:metadata'] = 'Le bloc Créateur de cours ne stocke pas de données personnelles.';
$string['headerconfig'] = 'Informations sur le fonctionnement du bloc';
$string['descconfig'] = 'Les cohortes proposées à l\'enseignant sont cherchées uniquement dans la catégorie de destination par défaut.\n'
        . ' Quand l\'enseignant renseigne un code Apogée, le bloc cherche d\'abord une cohorte correspondant au code + suffixe
            puis cherche sans si il n\'a rien trouvé.';
$string['introtextsettings'] = 'Texte en introduction du bloc';
$string['defaultdestinationcategory'] = 'Catégorie de destination par défaut';
$string['destinationcategories'] = 'Catégories de destination autorisées';
$string['origincategories'] = 'Catégories d\'origines autorisées';
$string['suffixcohort'] = 'Suffixe de la cohorte pour le code Apogée';
$string['mailrecipients'] = 'Destinataires du mail';
$string['requiredfieldform'] = 'Champ requis';
$string['newcoursename'] = 'Nom complet du cours';
$string['apogeecode'] = 'Indiquez le code APOGEE de votre cours<br>Retrouvez le code APOGEE correspondant sur'
        . ' <a target="_blank" rel="noopener noreferrer" href=https://dora.u-pem.fr/>DORA</a> ou contactez votre secrétariat ou indiquez un nom abrégé du cours.';
$string['validateform'] = 'Créer le cours';
$string['destinationcategory'] = 'Catégorie destination (par défaut l\'année en cours';
$string['importtext'] = 'Importez du contenu d\'un cours existant';
$string['nocopycourse'] = 'Nouveau cours, aucune importation';
$string['coursechoice'] = 'Choisissez votre contenu souhaité parmi vos cours dans le champ ci-dessous';
$string['addstudents'] = '<h3>Pour inscrire vos étudiants, renseignez  l\'un des deux champs suivants.</h3>';
$string['nocohort'] = 'Pas de sélection';
$string['cohortchoice'] = 'Choisissez de préférence le numéro d\'étape correspondant dans le champ ci-dessous<br>'
        . 'La liste est limitée à 26 propositions, indiquez les premiers caractères pour voir apparaitre la liste.';
$string['apogeecodestudent'] = 'Si vous ne le connaissez pas, indiquez le code étape (APOGEE) de la population';
$string['selectstudent'] = 'Inscrivez le nom suivi du prénom d\'un de vos étudiants et le
    programme essaiera de trouver le groupe d\'étudiant correspondant';
$string['commentteacher'] = 'Si vous ne trouvez pas ces informations, indiquez le nous dans le champ suivant'
        . ' ainsi que toutes informations supplémentaires à transmettre sur le cours.';
$string['mailsubject'] = 'Demande de création du cours {$a->coursename} par {$a->teacher}';
$string['messagestart'] = 'Bonjour,
L\'enseignant : {$a->teacher} a demandé(e) la création du cours :
Nom: {$a->coursename}
Nom court: {$a->courseshortname}';
$string['messagecourse'] = 'L\'enseignant a récupéré le contenu du cours suivant : {$a->courseurl}.';
$string['messagecohortlist'] = 'La cohorte {$a->name} a été inscrite depuis la liste.';
$string['messagecohortapogee'] = 'Le code apogée {$a->apogeecode} a été renseigné par l\'enseignant et la cohorte {$a->name} a donc été inscrite.';
$string['messagenocohortapogee'] = 'Le code apogée {$a->apogeecode} a été renseigné par l\'enseignant '
        . 'mais aucune cohorte correspondante n\'a été trouvée.';
$string['messagecohortstudent'] = 'L\'enseignant a indiqué que l\'étudiant {$a->studentname} suivait le cours et la cohorte'
        . ' {$a->name} a été inscrite en conséquence.';
$string['messagenocohortstudent'] = 'L\'enseignant a indiqué que l\'étudiant {$a->studentname} suivait le cours mais aucune cohorte'
        . ' avec cet étudiant n\'a été trouvée en conséquence, aucune cohorte n\'a été inscrite.';
$string['messagelistcohortstudent'] = 'L\'enseignant a indiqué que l\'étudiant {$a->studentname} suivait le cours. Plusieurs cohortes avec cet étudiant'
        . ' ont été trouvées, veuillez incrire la bonne cohorte dans le cours. La liste est : {$a->listcohorts}';
$string['messageerror'] = 'L\'inscription des cohortes ne correspond à aucun cas prévu, une erreur a du se produire,'
        . ' merci de râler gentiment sur le développeur.';
$string['messagecommentteacher'] = "L'enseignant a rajouté des informations que vous pouvez consulter ci-dessous : \n" . '{$a->commentteacher}';
$string['messageending'] = 'Vous pouvez consulter le nouveau cours à l\'adresse suivante : {$a->courseurl}.';
$string['couplingcohortstudent'] = 'Cohorte {$a->cohortname} avec l\'étudiant {$a->studentfirstname} {$a->studentmastname}';
$string['messageallcohortlist'] = 'La ou les cohortes suivantes ont été inscrites depuis la liste : {$a}';
$string['messageallstudentlist'] = 'La ou les cohortes suivantes ont été inscrites depuis la liste d\'étudiants : {$a}';
$string['messageallnostudentlist'] = 'Le ou les étudiants suivants ont été renseignés mais aucune cohorte n\'a été trouvé leur correspondant : {$a}';
$string['messagealltoomanystudentlist'] = 'Le ou les étudiants suivants ont été renseignés mais plusieurs cohortes ont été trouvés'
        . ' leur correspondant et aucune n\'a donc été inscrite : {$a}';


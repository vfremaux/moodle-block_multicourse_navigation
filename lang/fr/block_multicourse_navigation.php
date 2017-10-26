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
 * Defines the block strings
 *
 * @package     block_multicourse_navigation
 * @copyright   2016 onwards Valery Fremaux <http://docs.activeprolearn.com/en>
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['multicourse_navigation:addinstance'] = 'Ajouter un nouveau bloc de sommaire du cours';

$string['allmarks'] = 'Toutes les marques';
$string['anchortosection'] = 'Ancre de section';
$string['collapsed'] = 'Ouvrir';
$string['completion'] = 'Achèvement standard';
$string['config_blocktitle'] = 'Titre du bloc';
$string['config_blocktitle_default'] = 'Sommaire';
$string['config_initialcollapsed'] = 'Initiallement refermé';
$string['config_initialcollapsed_desc'] = 'Si actif, alors tous les items du bloc sont refermés à la première visite.';
$string['config_defaultignoremainsection'] = 'Ignorer la section 0';
$string['config_defaultignoremainsection_desc'] = 'Si activé, la section 0 du cours ne sera pas affichée.';
$string['config_defaultshowmodules'] = 'Afficher les activités (défaut)';
$string['config_defaultshowmodules_desc'] = 'Si activé, un nouveau bloc de sommaire multi-cours affichera les activités et ressources.';
$string['config_defaultshowsectionlinks'] = 'Afficher les liens vers les sections (défaut)';
$string['config_defaultshowsectionlinks_desc'] = 'Si activé, un nouveau bloc affichera les liens de sections.';
$string['config_ltccontract'] = 'Contrat d\'achèvement LTC';
$string['config_ignoremainsection'] = 'Ignorer la section 0';
$string['config_showmodules'] = 'Afficher les modules et ressources';
$string['config_showsectionlinks'] = 'Afficher les liens vers les sections';
$string['config_usecompletion'] = 'Affiche l\'achèvement';
$string['coursenoaccess'] = 'Vous n\'avez pas encore accès au cours';
$string['expanded'] = 'Fermer';
$string['learningtimecheck'] = 'Marques de temps pédagogiques (LTC)';
$string['mandatoryonly'] = 'Les marques obligatoires uniquement';
$string['nocompletion'] = 'Pas d\'achèvement';
$string['notusingsections'] = 'Ce format de cours n&rsquo;utilise pas de section.';
$string['onesectionview'] = 'Vue section unique';
$string['pluginname'] = 'Sommaire multi-cours';

$string['config_blocktitle_help'] = 'Laisser ce champ vide pour utiliser le nom par défaut comme titre du block. Si vous ajoutez un tittre ici,
il sera utilisée à la place de celui par défaut.';

$string['config_trackcourses_help'] = 'Entrez la liste d\'identifiant (numérique) de cours que vous voulez représenter. si elle
est vide, seul le cours courant sera représenté.';

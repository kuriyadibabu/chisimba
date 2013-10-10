<?php
/**
 * ahis partitions add Template
 *
 * Template to add partitions
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Joseph Gatheru
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('textarea','htmlelements');

if ($id) {
    $hStr = $this->objLanguage->languageText('word_edit')." ".$this->objLanguage->languageText('mod_ahis_speciesagegrps','openaris');
    $objFormUri = $this->uri(array('action'=>'speciesagegroup_save', 'id'=>$id));
    $record = $this->objPartition->getRow('id', $id);
} else {
    $hStr = $this->objLanguage->languageText('word_add')." ".$this->objLanguage->languageText('mod_ahis_speciesagegrps','openaris');
    $objFormUri = $this->uri(array('action'=>'speciesagegroup_save'));
    $record['name'] = '';
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $hStr;


$sButton = new button('enter', $this->objLanguage->languageText('word_enter'));
$sButton->setToSubmit();
$backUri = $this->uri(array('action'=>'speciesagegroup_admin'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$sButton->setCSS('saveButton');
$bButton->setCSS('backButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;

//species age group
$label = new label ('Species Age Group: ', 'agegroup');
$agegroup = new textinput('agegroup');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($agegroup->show());
$objTable->endRow();

//species id
$label = new label ('Species: ', 'speciesid');

$speciesid = new dropdown('speciesid');
$speciesid->addFromDB($species, 'speciesname', 'id');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($speciesid->show());
$objTable->endRow();

// abbreviation	
$label = new label ('Abbreviation: ', 'abbreviation');
$abbreviation = new textinput('abbreviation');
$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($abbreviation->show());
$objTable->endRow();

//description
$label = new label ('Description: ', 'description');
$description = new textarea('description');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($description->show());
$objTable->endRow();

//lower limit
$label = new label ('Lower Limit: ', 'lowerlimit');
$lowerlimit = new textinput('lowerlimit');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($lowerlimit->show());
$objTable->endRow();

//upper limit
$label = new label ('Upper Limit: ', 'upperlimit');
$upperlimit = new textinput('upperlimit');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($upperlimit->show());
$objTable->endRow();

//start date
$label = new label ('Start Date: ', 'startdate');
$startdate = new textinput('startdate');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($startdate->show());
$objTable->endRow();
		
//end date
$label = new label ('End Date: ', 'enddate');
$enddate = new textinput('enddate');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($enddate->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($bButton->show());
$objTable->addCell($sButton->show());
$objTable->endRow();

$objForm = new form('speciesagegroupadd', $objFormUri);

//form validations
$objForm->addRule('species', $this->objLanguage->languageText('mod_ahis_speciesiderror','openaris'),'required');
$objForm->addRule('agegroup', $this->objLanguage->languageText('mod_ahis_speciesagegrouperror','openaris'),'required');


$objForm->addToForm($objTable->show());
$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();
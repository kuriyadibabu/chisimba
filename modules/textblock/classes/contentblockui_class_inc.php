<?php

/**
 *
 * User Interface code for ContentBlocks
 *
 * User Interface code for ContentBlocks. This class builds the user interface
 * elements of the ContentBlocks module.
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
 * @package   contentblocks
 * @author    Paul Mungai paulwando@gmail.com
 * @copyright 2012 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * User Interface code for ContentBlocks
 *
 * User Interface code for ContentBlocks. This class builds the user interface
 * elements of the ContentBlocks module.
 *
 * @package   ContentBlocks
 * @author    Paul Mungai paulwando@gmail.com
 *
 */
class contentblockui extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     *
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     *
     */
    public $objUser;
    public $mode = FALSE;
    public $title = NULL;
    public $blockid = NULL;
    public $blocktext = NULL;

    /**
     *
     * Constructor for the textblockui object
     *
     * @access public
     * @return VOID
     *
     */
    public function init() {
        //Create an instance of the database class for this module
        $this->objDb = $this->getObject("dbcontentblocks", "contentblocks");

        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->mode = $this->getParam('mode', 'add');
        if ($this->mode == 'edit') {
            $id = $this->getParam('id', NULL);
            $this->loadData($id);
        }
        $this->loadClass('link', 'htmlelements');
    }

    /**
     * Show all Widetext blocks as a list, with the content rolled up
     * under the title of the block.
     *
     * @return type string The listed items
     * @access public
     *
     */
    public function showWideTextListed() {
        return $this->showAllBlocks("content_widetext");
    }

    /**
     * Show all narrow contentblocks as a list, with the content rolled up
     * under the title of the block.
     *
     * @return type string The listed items
     * @access public
     *
     */
    public function showNarrowTextListed() {
        return $this->showAllBlocks("content_text");
    }

    /**
     *
     * Show all blocks of a particular type (text, or widetext)
     * 
     * @param string $blockType  The type of block (text, or widetext)
     * @return string A rendered table of blocks with clickable titles
     * @access public 
     * 
     */
    public function showAllBlocks($blockType) {
        $ar = $this->objDb->getBlocksArr($blockType);
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 10;
        $icon = $this->getObject('image', 'htmlelements');
        $icon->height = "31px";
        $icon->width = "32px";
        $icon->src = $this->getResourceUri('images/block-32.png', 'contentblocks');
        $iconVw = $icon->show();
        if (!empty($ar)) {
            foreach ($ar as $block) {
                $title = $block['title'];
                $blockid = $block['blockid'];
                $id = $block['id'];
                // Make title clickable to show the contents
                $title = "<a href=\"javascript:void(0);\" class=\"BLOCK_TITLE\" id=\"BLOCK_$id\">$title</a>";
                $content = $block['blocktext'];
                $content = $this->renderContent($content);
                $content = $this->putInDiv($content, $id);
                $table->startRow(NULL, "ROW_" . $id);
                $table->addCell($iconVw, 30);
                $table->addCell($title . $content);
                $table->addCell(
                        $this->insertEditIcon($id)
                        . "&nbsp;"
                        . $this->insertDelIcon($id), 60
                );
                $table->endRow();
            }
        }
        $ret = $this->makeHeadingListed($blockType);
        $ret .= $table->show();
        return $ret;
    }

    /**
     *
     * Put the content in a div with the ID for the 
     * Javascript to have access.
     * 
     * @param string $content The content
     * @param string $id The record id
     * @return string The div with the id and content
     * @access public
     * 
     */
    public function putInDiv($content, $id) {
        return "<div id='BLCONT_$id' class='BLOCK_CONTENT' style='display: none'>$content</div>";
    }

    /**
     *
     * Make a heading for the form
     *
     * @param string $type The type of block
     * @return string The text of the heading
     * @access private
     *
     */
    private function makeHeadingListed($type) {
        $h = "";
        $this->loadClass('htmlheading', 'htmlelements');
        // Get heading based on whether it is wide or narrow.
        if ($type == 'content_text') {
            $h = $this->objLanguage->languageText(
                            'mod_contentblocks_heading_text', 'contentblocks', "Side contentblocks");
        } elseif ($type == 'content_widetext') {
            $h = $this->objLanguage->languageText(
                            'mod_contentblocks_heading_widetext', 'contentblocks', "Wide contentblocks");
        }
        // Setup and show heading.
        $header = new htmlHeading();
        $header->str = $h . $this->insertAddIcon(TRUE, $type);
        $header->type = 2;
        return $header->show();
    }

    /**
     *
     * Insert an add icon for use by javacript. It will be visitble
     * when you are in edit mode, but invisible when you are in add 
     * mode. After a save, it will be toggled to visible.
     * 
     * @param string $mode The edit|add mode
     * @return string The rendered icon
     * @access private
     *  
     */
    private function insertAddIcon($visible=TRUE, $blockType="content_text") {
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $link = $this->uri(
                        array("action" => "ajaxedit",
                            "mode" => "add",
                            "blocktype" => $blockType),
                        'contentblocks');
        $addlink = new link($link);
        $objIcon->setIcon('add');
        $addlink->link = $objIcon->show();
        if ($visible == TRUE) {
            $showCss = "style='visibility:show'";
        } else {
            $showCss = " style='visibility:hidden' ";
        }
        return "&nbsp; <span class='conditional_add' "
        . $showCss . ">" . $addlink->show()
        . "</span>";
    }

    /**
     *
     * Insert a delete icon with a null javascript link so
     * it can be grabbed by Javascript
     * 
     * @param string $id The id of the record
     * @return string The icon with link
     * @access public
     *  
     */
    public function insertDelIcon($id) {
        if ($this->objUser->isAdmin()) {
            $delIcon = $this->newObject('geticon', 'htmlelements');
            $delIcon->setIcon('delete');
            $delUrl = 'javascript:void(0);';
            $delLink = new link($delUrl);
            $delLink->cssId = $id;
            $delLink->cssClass = "dellink";
            $delLink->link = $delIcon->show();
            return $delLink->show();
        } else {
            return NULL;
        }
    }

    /**
     *
     * Insert an edit icon with a link to the edit action 
     * 
     * @param string $id The id of the record
     * @return string The icon with link
     * @access public
     *  
     */
    public function insertEditIcon($id) {
        $edIcon = $this->newObject('geticon', 'htmlelements');
        $edIcon->setIcon('edit');
        if ($this->objUser->isAdmin()) {
            $blockType = $this->getParam("action", "content_text");
            $edUrl = $this->uri(array(
                        'action' => 'ajaxedit',
                        'mode' => 'edit',
                        'blocktype' => $blockType,
                        'id' => $id
                            )
            );
            $edLink = new link($edUrl);
            $edLink->link = $edIcon->show();
            return $edLink->show();
        } else {
            return NULL;
        }
    }

    /**
     *
     * Left navigation panel for textblocks
     *
     * @return string Formatted left navigation bloci
     * @access public
     *
     */
    public function showLeftNav() {
        $narrowUri = $this->uri(array(
                    'action' => 'content_text'
                        ), 'contentblocks');
        $wideUri = $this->uri(array(
                    'action' => 'content_widetext'
                        ), 'contentblocks');
        $icon = $this->getObject('image', 'htmlelements');
        $icon->height = "15px";
        $icon->width = "16px";
        $icon->src = $this->getResourceUri('images/block-16.png', 'contentblocks');
        $iconVw = $icon->show();
        $this->loadClass('link', 'htmlelements');
        // Narrow blocks.
        $narrowLink = new link($narrowUri);
        $narrowLink->cssId = 'narrow';
        $narrowLink->cssClass = 'navlink';
        $narrowLink->link = $this->objLanguage->languageText(
                        "mod_contentblocks_heading_text", "contentblocks");
        $narrowNav = $narrowLink->show();
        // Wide blocks.
        $wideLink = new link($wideUri);
        $wideLink->cssId = 'wide';
        $wideLink->cssClass = 'navlink';
        $wideLink->link = $this->objLanguage->languageText(
                        "mod_contentblocks_heading_widetext", "contentblocks");
        $wideNav = $wideLink->show();
        return $iconVw . "&nbsp;"
        . $narrowNav . "<br />"
        . $iconVw . "&nbsp;"
        . $wideNav;
    }

    /**
     *
     * For editing, load the data according to the ID provided. It
     * loads the data into object properties.
     *
     * @param string $id The id of the record to load
     * @return boolean TRUE|FALSE
     * @access private
     *
     */
    private function loadData($id) {
        $objDb = $this->getObject("dbcontentblocks", "contentblocks");
        $arData = $objDb->getBlockById($id);
        if (!empty($arData)) {
            foreach ($arData[0] as $key => $value) {
                $this->$key = $value;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Render the edit form
     * 
     * @return string The rendered form
     * @access public
     *  
     */
    public function showEditForm() {
        // Serialize language items to Javascript.
        $arrayVars['titlereq'] = "mod_contentblocks_titlereq";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'contentblocks');

        // Set the mode.
        $mode = $this->mode;
        $id = $this->getParam('id', NULL);
        // Load the jquery validate plugin.
        $this->appendArrayVar('headerParams',
                $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
                        'jquery'));
        // Load the edit helper Javascript.
        $this->appendArrayVar('headerParams',
                $this->getJavaScriptFile('editblock.js',
                        'contentblocks'));
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');

        // Create the form.
        $objForm = new form('blockeditor');
        $paramArray = array(
            'action' => 'save',
            'mode' => $mode);
        $formAction = $this->uri($paramArray);
        $objForm->setAction($formAction);
        $objForm->displayType = 3;

        // Create an element for the hidden text input for $id.
        $objElement = new textinput("id");
        $objElement->setValue($id);
        $objElement->fldType = "hidden";
        $objForm->addToForm($objElement->show());

        // Dropdown for the blockid.
        $wsiLabel = new label(
                        $this->objLanguage->languageText(
                                'mod_contentblocks_field_blockid', 'contentblocks'),
                        "input_blockid");
        $blockType = $this->getParam('blocktype', 'content_text');

        // Create an element for the hidden text input for $id.
        $objElement1 = new textinput("blockid");
        $objElement1->setValue($blockType);
        $objElement1->fldType = "hidden";
        $objForm->addToForm($objElement1->show());
        // Create an element for the input of title.
        $objElement = new textinput("title");
        if (isset($this->title)) {
            $objElement->setValue($this->title);
        }
        $wsiLabel = new label(
                        $this->objLanguage->languageText(
                                'mod_contentblocks_field_title', 'contentblocks'),
                        "input_title");

        // Checkbox to toggle title display.
        if (isset($this->showTitle)) {
            if ($this->showTitle == '') {
                $showTitle = TRUE;
            }
        } else {
            $showTitle = TRUE;
        }
        $objCheck = new checkbox('show_title',
                        $this->objLanguage->languageText('mod_contentblocks_show_title',
                                'contentblocks'), $showTitle);
        $wsiQuestionLabel = new label(
                        $this->objLanguage->languageText(
                                'mod_contentblocks_show_title', 'contentblocks'),
                        "input_title");
        $objForm->addToForm($wsiLabel->show()
                . "&nbsp;&nbsp;&nbsp;"
                . $objCheck->show()
                . "<br />" . $objElement->show()
                . "<br /><br />");

        // Create input for an alternative cssClass to use.
        $objElement = new textinput("css_class");
        // Set the value of the element to the chosen cssClass.
        if (isset($this->cssClass)) {
            $objElement->setValue($this->cssClass);
        }
        $wsiLabel = new label(
                        $this->objLanguage->languageText(
                                'mod_contentblocks_css_class', 'contentblocks'),
                        "input_title");
        $objForm->addToForm($wsiLabel->show()
                . " <br />" . $objElement->show()
                . "<br /><br />");
        $objElement->extra = '';

        // Create input for an alternative cssId to use.
        $objElement = new textinput("css_id");
        if (isset($this->cssId)) {
            $objElement->setValue($this->cssId);
        }
        $wsiLabel = new label(
                        $this->objLanguage->languageText(
                                'mod_contentblocks_css_id', 'contentblocks'),
                        "input_title");
        $objForm->addToForm($wsiLabel->show()
                . " <br />" . $objElement->show()
                . "<br /><br />");

        // Create an element for the input of block text.
        /* $objElement = new textarea("blocktext");
          $objElement->rows = 10;
          //Set the value of the element to $title
          if (isset($this->blocktext)) {
          $objElement->setContent(htmlspecialchars($this->blocktext));
          } */
        $objElement = $this->newObject('htmlarea', 'htmlelements');
        $objElement->name = 'blocktext';
        $objElement->width = '450px';
        $objElement->height = '250px';
        $objElement->toolbarSet = 'simple';
        
        //Set the value of the element to $title
        if (isset($this->blocktext)) {
            $objElement->value = $this->blocktext;
        }

        //Create label for the input of quote
        $quoteLabel = new label(
                        $this->objLanguage->languageText(
                                'mod_contentblocks_field_blocktext', 'contentblocks'),
                        "input_blocktext");
        $objForm->addToForm($quoteLabel->show()
                . "<br />" . $objElement->show()
                . "<br /><br />");
        // Make a hidden field for the type of block
        $hidMode = new hiddeninput('blocktype');
        $hidMode->value = $blockType;
        $objForm->addToForm($hidMode->show());

        // Make a save button.
        $objElement = new button('submit');
        $objElement->setToSubmit();
        $objElement->setValue(' '
                . $this->objLanguage->languageText("word_save")
                . ' ');
        $objForm->addToForm('<br />' . $objElement->show() . "<br />");

        //Add the heading to the layer
        $this->objH = $this->getObject('htmlheading', 'htmlelements');
        $this->objH->type = 1;
        if ($mode == 'edit') {
            $this->objH->str = $this->objLanguage->languageText("mod_contentblocks_edittitle", 'contentblocks');
        } else {
            $this->objH->str = $this->objLanguage->languageText("mod_contentblocks_addtitle", 'contentblocks');
        }
        // Send it back.
        return $this->objH->show()
        . "<br />\n\n"
        . $objForm->show();
    }

    /**
     *
     * Render the content to parse filters
     * 
     * @param string $content The content to render
     * @return string The parsed content
     * @access public
     * 
     */
    public function renderContent($content) {
        $objWashout = $this->getObject("washout", "utilities");
        return $objWashout->parseText($content);
    }

    /**
     *
     * Get an array of unused blocks
     * 
     * @param type $blockType
     * @return type 
     */
    public function getAvailableBlocks($blockType) {
        $numOfBlocks = 60;
        // Build an array of potentially available blocks.
        $counter = 1;
        while ($counter <= 60) {
            $rootBlocks[] = $blockType . $counter;
            $counter++;
        }
        // Get an array of the used blocks.
        $objDb = $this->getObject("dbcontentblocks");
        $usedBlocks = $objDb->getArUsedBlockss($blockType);
        // Need to feed the diff an empty array if none are used.
        if (empty($usedBlocks)) {
            $usedBlocks = array();
        }
        $avail = array_diff($rootBlocks, $usedBlocks);
        return $avail;
    }

}

?>
<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * HTML Base Class forms the base for 
 * most html objects .It hold most of the 
 * common functionality and variables needed for 
 * a html object
 *
 * @category  HTML Controls

 * @package   htmlelements
 * @author    Wesley Nitsckie
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class htmlbase extends object
{

    /**
    * @var string $cssId: A unique id for the element, often corresponding to a 
    *             #id tag in a stylesheet. In KEWL.NextGen it is the CSS ID used from 
    *             the skin stylesheet
    *             
    *             Not valid in base, head, html, meta, param, script, style, and title elements.
    *             
    */
    var $cssId;
    /**
    * 
    * @var string $cssClass: The class of the element, in KEWL.NextGen it is the CSS 
    *             Class from the skin stylesheet
    *             
    *             Not valid in base, head, html, meta, param, script, style, and title elements.
    *             
    */
    var $cssClass;
    /**
    * 
    * @var string $title: The title of the anchor. 
    *             Note: Not allowed if DTD is strict, only in transitional and frameset DTDs 
    *             Optional
    */
    var $title;
    /**
    * @var string $style: An inline style definition 
    */
    var $style;
    /**
    * @var string $dir: Sets the text direction ltr | rtl
    */
    var $dir;
    /**
    * @var string $lang: Sets the language code 
    */
    var $lang;
    /**
    * @var string $tabindex: Sets the tab order of an element
    */
    var $tabindex;
    /**
    * @var string $accesskey: Sets a keyboard shortcut to access an element
    */
    var $accesskey;
    /**
    * 
    * @var string $name The name of the element
    */
    var $name;
    /**
    * 
    * @var string $extra: anything extra that you want to add, such
    *             as an additional style
    */
    var $extra;  
    
    /**
    * Constructor
    */
    function htmlbase($name)
    {
        //set the name of the element
        $this->name=$name;
    }
}

?>
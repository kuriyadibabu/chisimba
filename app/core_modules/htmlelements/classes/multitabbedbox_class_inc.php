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

// Include the HTML base class

/**
 * Description for require_once
 */
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* HTML control class to create multiple tabbed boxes using the layers class.
* The style sheet class is >box<.
* 
* 
* @package   htmlelements
* @category  Chisimba
* @author    Wesley Nitsckie
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
* @example  
*            $objElement =new multitabbedbox(100,500);
*            $objElement->addTab(array('name'=>'First','url'=>'http://localhost','content' => $form,'default' => true));
*            $objElement->addTab(array('name'=>'Second','url'=>'http://localhost','content' => $check.$radio.$calendar));
*            $objElement->addTab(array('name'=>'Third','url'=>'http://localhost','content' => $tab,'height' =>
*         '300','width' => '600'));        
*/

class multitabbedbox extends abhtmlbase implements ifhtml
{
    /**
    * @var $tabs array :  Array that holds all the tabs
    */
    public $tabs=array();

    /**
    * @var $height int :  the height all the tabs
    */
    public $height;

    /**
    * @var $width int : with of all the tabs
    */
    public $width;    
    
    /**
    * Constuctor
    * @param int $height (optional);
    * @param int $width (optional);
    */
    public function multitabbedbox($height=100,$width=500)
    {
        $this->height=$height;
        $this->width=$width;    
    }
    
    /**
    * Method that add a tab
    * @param $properties array : Can hold the following values
    * name string
    * content string
    * url string
    * default boolean
    */    
    public function addTab($properties=NULL){
        if (is_array($properties)) {
            if (isset($properties['name'])) {                
                $this->tabs[$properties['name']]['name']=$properties['name'];
                if(isset($properties['content']))
                    $this->tabs[$properties['name']]['content']=$properties['content'];
                if(isset($properties['url']))
                    $this->tabs[$properties['name']]['url']=$properties['url'];
                if(isset($properties['default']))
                    $this->tabs[$properties['name']]['isDefault']=$properties['default'];                      
                if(isset($properties['width']))
                    $this->tabs[$properties['name']]['width']=$properties['width'];                      
                if(isset($properties['height']))
                    $this->tabs[$properties['name']]['heigth']=$properties['height'];            
            }            
        }        
    }
    
    /**
    * Method to show the tabs
    * @return $str string
    */
    public function show(){
        $cnt=0;$str='';$width='';
        //get the javascript
        $str='<script language="JavaScript" src="core_modules/htmlelements/resources/tabbedbox.js"></script>';
        
        //start the big div box
        $str.="<div class=\"multibox\">\n";
        foreach($this->tabs as $tab){
            $cnt=$cnt+1;
            $str.="<a href=\"javascript:;\" onclick=\"showmenu('box$cnt')\">";
            $str.="<span id=\"label$cnt\" ";
            if(isset($tab['isDefault'])){
                $str.= "class=\"multitabselected\"";
            }
            else
                $str.= "class=\"multitablabel\"";            
            $str.=">";
            $str.=$tab['name']."</span></a>\n";
            $width=$width+strlen($tab['name'])+2;
        }
    
//        $width=$width*10;
        $cnt=0;
        $str.='<br />';
        foreach($this->tabs as $tab){
            $cnt=$cnt+1;
            
            //get the width for the each tab
            if (isset($tab['width'])) {
                $width=$tab['width'];
            } else {
                //use the default width if tab was not set
                $width=$this->width;
            }
            
            //get the height for each tab
            if (isset($tab['height'])) {
                $height=$tab['height'];
            } else {
                //use the default height if tab was not set
                $height=$this->height;
            }
            
            $str.="<div id=\"box$cnt\" class=\"multibox-content\" style=\"position:absolute; width:".$width."; height:".$height.";  z-index:0; visibility: ";
            if (isset($tab['isDefault'])) {
                $str.=($tab['isDefault'])?'visible':'hidden';   
            }
            else{
                $str.='hidden';
            }
            $str.="\">\n";
            $str.=$tab['content']."</div>\n";
        }
        $str.="</div>";
        return $str;
    }    
} 
?>
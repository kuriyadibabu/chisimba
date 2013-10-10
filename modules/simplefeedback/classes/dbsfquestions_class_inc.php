<?php
/**
 *
 * Database access for Simple feedback questions
 *
 * Database access for Simple feedback questions. This is a database model class
 * that provides data access to the default module table - tbl_simplefeedback_text.
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
 * @package   simplefeedback
 * @author    Derek Keats derekkeats@gmail.com
 * @copyright 2007 AVOIR
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
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 *
 * Database access for Simple feedback questions
 *
 * Database access for Simple feedback questions. This is a database model class
 * that provides data access to the default module table - tbl_simplefeedback_text.
*
* @package   simplefeedback
* @author    Derek Keats derekkeats@gmail.com
*
*/
class dbsfquestions extends dbtable
{

    /**
    *
    * Intialiser for the simplefeedback database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_simplefeedback_questions');
    }
    
    public function getSurvey($surveyId)
    {
        return $this->getall(" WHERE surveyid='$surveyId' ");
    }
}
?>
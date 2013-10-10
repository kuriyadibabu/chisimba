<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Pdf_Element */
require_once 'modules/pdf/resources/Element.php';

/** Zend_Pdf_Element_Object */
require_once 'modules/pdf/resources/Element/Object.php';



/**
 * Container which collects updated object info.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_UpdateInfoContainer
{
    /**
     * Object number
     *
     * @var integer
     */
    private $_objNum;

    /**
     * Generation number
     *
     * @var integer
     */
    private $_genNum;


    /**
     * Flag, which signals, that object is free
     *
     * @var boolean
     */
    private $_isFree;

    /**
     * String representation of the object
     *
     * @var string
     */
    private $_dump;

    /**
     * Object constructor
     *
     * @param integer $objCount
     */
    public function __construct($objNum, $genNum, $isFree, $dump = null)
    {
        $this->_objNum = $objNum;
        $this->_genNum = $genNum;
        $this->_isFree = $isFree;
        $this->_dump   = $dump;
    }


    /**
     * Get object number
     *
     * @return integer
     */
    public function getObjNum()
    {
        return $this->_objNum;
    }

    /**
     * Get generation number
     *
     * @return integer
     */
    public function getGenNum()
    {
        return $this->_genNum;
    }

    /**
     * Check, that object is free
     *
     * @return boolean
     */
    public function isFree()
    {
        return $this->_isFree;
    }

    /**
     * Get string representation of the object
     *
     * @return string
     */
    public function getObjectDump()
    {
        return $this->_dump;
    }
}


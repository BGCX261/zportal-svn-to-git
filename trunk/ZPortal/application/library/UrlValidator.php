<?php
/**
 * Zend Portal
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
 * @category   Zend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author 
 */

include_once 'Zend/Validate/Abstract.php';
include_once 'Zend/Uri.php';

class UrlValidator extends Zend_Validate_Abstract
{

    protected $_messageTemplates = array('invalid' => "'%value%' is not a valid URL" , 'inaccessible' => "Unable to access URL '%value%'");

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid and existing URL
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $value = (string) $value;
        if (!Zend_Uri::check($value)) {
            $this->_error('invalid', $value);
            return false;
        }
        $fp = @fopen($value, 'r');
        if (!$fp) {
            $this->_error('inaccessible', $value);
            return false;
        }
        
        return true;
    }
}
?>
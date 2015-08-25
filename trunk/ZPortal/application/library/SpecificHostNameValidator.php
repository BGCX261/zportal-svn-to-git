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

require_once ('Zend/Validate/Abstract.php');

class Zend_Validate_SpecificHostNameValidator extends Zend_Validate_Abstract
{

    protected $_messageTemplates = array('invalid' => "'%value%' is not a valid hostname");

    /**
     * @var string - the host name that we validated 
     */
    protected $hostname;

    function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @see Zend_Validate_Interface::isValid()
     *
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $value = (string) $value;
        $splitted = explode('@', $value);
        
        // validates that the value is not empty and consists of a '$hostname' DNS 
        if ($splitted && (count($splitted) == 2)) {
            if ($splitted[1] == $this->hostname) {
                return true;
            } else {
                $this->_error('invalid', $splitted[1]);
                return false;
            }
        }
        return false;
    }
}
?>

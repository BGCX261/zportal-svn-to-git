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
include_once 'application/library/ConfirmPasswordValidator.php';
include_once 'application/library/RoleValidator.php';
include_once 'application/library/SpecificHostNameValidator.php';
include_once 'Zend/Validate/StringLength.php';
include_once 'Zend/Filter/Input.php';
include_once 'Zend/Db/Table/Abstract.php';
include_once 'UserRow.php';

class Users extends Zend_Db_Table_Abstract
{

    const MIN_PASSWORD_LENGTH = 4;

    const MAX_PASSWORD_LENGTH = 20;

    protected $_name = 'users';

    protected $_rowClass = 'UserRow';

    public function getByEmail($email)
    {
        $user = $this->fetchRow(array('email = ?' => $email));
        if ($user) {
            return $user->toArray();
        }
        return null;
    }

    /**
     * @param $data array
     * @return Zend_Filter_Input|int - the id of the new user or -1 if invalid input 
     */
    public function addUser($data)
    {
        $filteredData = $this->filterUserInformation($data);
        if (!$filteredData->isValid()) {
            return $filteredData;
        }
        
        // gets the validated and filtered fields
        $validFields = $filteredData->getUnescaped();
        
        // inserts the filtered data to the table
        $row = $this->createRow(array_merge($data, $validFields));
        return $row->save();
    }

    /**
     * @param $data array 
     * @return Zend_Filter_Input filter input of the data
     */
    protected function filterUserInformation($data)
    {
        $filters = array('email' => array('StringTrim' , 'StringToLower'));
        $validators = array('email' => array('EmailAddress' , new Zend_Validate_SpecificHostNameValidator('zend.com')) , 'role' => 'RoleValidator');
        return new Zend_Filter_Input($filters, $validators, $data);
    }

    /**
     * Update the user information
     * @param $data array
     * @param $user - the user to update
     */
    public function updateUser(array $data)
    {
        $roles = UserRow::getRoles();
        $data['role'] = $roles[$data['role']];
        try {
            $row = $this->fetchRow(array('email = ?' => $data['email']));
            if (!$row) {
                throw new Zend_Exception('Fails to update - no such registered user ' . $data['email']);
            }
            $row->role = $data['role'];
            $row->save();
        } catch (Exception $e) {
            throw new Zend_Exception('Fails to update' . $e->getMessage());
        }
    }
}
?>

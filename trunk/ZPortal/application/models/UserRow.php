<?php
include_once 'Zend/Db/Table/Row/Abstract.php';

class UserRow extends Zend_Db_Table_Row_Abstract
{

    /**
     * @return array possible roles of a user
     */
    public static function getRoles()
    {
        return array('admin' , 'member');
    }
}
?>
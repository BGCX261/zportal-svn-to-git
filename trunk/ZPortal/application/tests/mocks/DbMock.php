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

require_once ('Zend/Db/Adapter/Abstract.php');

/**
 *
 */
class DbMock extends Zend_Db_Adapter_Abstract
{

    public function __construct()
    {}

    public function listTables()
    {
        return array('dummy');
    }

    public function describeTable($tableName, $schemaName = null)
    {
        return array('column' => array('SCHEMA_NAME' => $schemaName , 'TABLE_NAME' => $tableName , 'COLUMN_NAME' => 'column' , 'COLUMN_POSITION' => 1 , 'DATA_TYPE' => 'VARCHAR' , 'DEFAULT' => null , 'NULLABLE' => true , 'LENGTH' => 10 , 'SCALE' => null , 'PRECISION' => null , 'UNSIGNED' => null , 'PRIMARY' => true , 'PRIMARY_POSITION' => 1));
    }

    public function prepare($sql)
    {
        return new Zend_Db_Statement_Mock($sql);
    }

    public function lastInsertId($tableName = null, $primaryKey = 'id')
    {}

    public function setFetchMode($mode)
    {
        return;
    }

    public function limit($sql, $count, $offset = 0)
    {}

    public function supportsParameters($type)
    {
        return true;
    }

    public function closeConnection()
    {
        $this->_connection = null;
    }

    protected function _checkRequiredOptions(array $config)
    {
        return true;
    }

    protected function _connect()
    {
        $this->_connection = $this;
    }

    protected function _beginTransaction()
    {
        return true;
    }

    protected function _commit()
    {
        return true;
    }

    protected function _rollBack()
    {
        return true;
    }
}
?>

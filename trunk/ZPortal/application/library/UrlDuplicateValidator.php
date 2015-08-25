<?php
include_once 'Zend/Validate/Abstract.php';

class UrlDuplicateValidator extends Zend_Validate_Abstract
{

    private $table;

    private $id;

    public function __construct($table, $id = null)
    {
        $this->table = $table;
        $this->id = $id;
    }

    protected $_messageTemplates = array('duplicate' => "'%value%' is duplicate");

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
        if ($this->table->fetchRow(array('url = ?' => $value) + ($this->id ? array('id != ?' => $this->id) : array()))) {
            $this->_error('duplicate', $value);
            return false;
        }
        return true;
    }
}

?>

<?php

/**
 * Channels
 *  
 * @author yuval
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Channels extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name='channels';
	protected $_primary='id';
	
	/**
	 * @param $title - channel title
	 * @param $uid - usr ID
	 * @return mixed (FALSE or channle id)
	 */
	public function addChannel($title, $uid) {
		$row = $this->createRow();
		if( empty($title)) {return false;}
		$row->title = $title;
		$row->managing_editor = $uid;		
        $retVal = $row->save();
        $row->link = "channel_id/{$retVal}";
        return $row->save();
	}
    /**
     * @param $id
     * @param $where more conditions
     */
    public function get($id, array $where = array())
    {
        $row = $this->fetchRow(array('id = ?' => $id) + $where);
        return $row ? $row->toArray() : null;
    }
	
}

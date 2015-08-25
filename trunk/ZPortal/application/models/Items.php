<?php

/**
 * Items
 *  
 * @author yuval
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Items extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name='items';
	protected $_primary='id';
	
	/**
	 * @param $channel_id 
	 * @param $title
	 * @param $uid
	 * @return mixed FALSE or item ID
	 */
	public function addItem($channel_id, $title, $uid) {
		$row = $this->createRow();
		$row->channel_id = $channel_id;
		$row->title=$title;
		$row->author=$uid;
        $retVal = $row->save();
        $row->link = "channel_id/{$channel_id}/item_id/{$retVal}";
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

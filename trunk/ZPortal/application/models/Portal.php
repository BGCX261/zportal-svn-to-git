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

include_once 'Zend/Db/Table/Abstract.php';

class Portal extends Zend_Db_Table_Abstract
{

    protected $_name = 'portal';

    /**
     * @var Feeds
     */
    protected $feeds = null;

    function __construct($feedsModel)
    {
        parent::__construct();
        $this->feeds = $feedsModel;
    }

    public function getSubscriptions($userId)
    {
        $rowSet = $this->fetchAll(array('user_id = ?' => $userId));
        
        $feeds = array();
        foreach ($rowSet as $row) {
            $feeds[] = $this->feeds->get($row->feed_id);
        }
        return $feeds;
    }
    
    public function getSubscriptionsScheme($userId)
    {
        $rowSet = $this->fetchAll(array('user_id = ?' => $userId));
        
        $feeds = array();
        foreach ($rowSet as $row) {
            $feeds[] = array($row->feed_id, $this->feeds->getTitle($row->feed_id));
        }
        return $feeds;
    }
    

    /**
     * @param $userId
     * @param feedId 
     * @param $where more conditions
     */
    public function get($userId, $feedId, array $where = array())
    {
        $row = $this->fetchRow(array('user_id = ?' => $userId , 'feed_id = ?' => $feedId) + $where);
        return $row ? $row->toArray() : null;
    }

}

?>
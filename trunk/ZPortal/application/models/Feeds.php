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

require_once 'Zend/Config/Ini.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Cache.php';
include_once 'Zend/Date.php';
include_once 'Zend/Feed.php';
include_once 'application/library/UrlDuplicateValidator.php';
include_once 'Zend/Db/Table/Abstract.php';
include_once 'application/library/UrlValidator.php';
include_once 'Zend/Filter/Input.php';
include_once 'Zend/Exception.php';

class FeedReadException extends Exception {
	
}
class Feeds extends Zend_Db_Table_Abstract
{

    protected $_name = 'feeds';

    const UPDATE_FREQUENCY = 15; // 15 min

    
    /**
     * marks the id for root group id
     */
    const ROOT_GROUP_ID = 1;

    public function getGroupNames()
    {
        $rows = $this->fetchAll(array('type = ?' => 'group'), 'name')->toArray();
        $groups = array();
        foreach ($rows as $group) {
            $groups[$group['id']] = $group['name'];
        }
        return $groups;
    }

    public function updateFeed($data)
    {
        $input = $this->createDataInput($data);
        if (!$input->isValid()) {
            return $input;
        }
        $row = $this->createRow(array_merge($data, $input->getUnescaped()))->toArray();
        if (@$row['id']) {
            $this->update($row);
            return $row['id'];
        } else {
            $this->insert($row);
            return $this->getAdapter()->lastInsertId();
        }
    }

    /**
     * @return Zend_Filter_Input
     */
    public function createDataInput($data)
    {
        $filters = array('name' => 'StringTrim' , 'group_id' => 'Digits');
        $validators = array();
        if (!$isGroup = ($data['type'] == 'group')) {
            $filters['url'] = 'StringTrim';
            $validators['url'] = array(new UrlValidator() , new UrlDuplicateValidator($this, @$data['id']));
        }
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($data);
        return $input;
    }

    public function get($id, array $where = array())
    {
        $feed = $this->fetchRow(array('id = ?' => $id) + $where);
        if (!$feed) {
            return null;
        }
        
        // get the feed path  
        if (!$feed->path) {
            $feed->path = $this->calculatePath($feed);
            // insert path to DB
            $feed->save();
        }
        
        return $feed->toArray();
    }
    
    public function getTitle($id) 
    {
        $feed = $this->fetchRow(array('id = ?' => $id));
        if (!$feed) {
            return null;
        }
        
        // get the feed path  
        return $feed->name;
    }

    public function getFeed($id)
    {
        return $this->get($id, array('type = ?' => 'feed'));
    }

    public function getGroup($id)
    {
        return $this->get($id, array('type = ?' => 'group'));
    }

    /**
     * @param $groupId
     */
    public function listParents($id)
    {
        $current = $id;
        $result = array();
        
        while ($current !== null) {
            $feed = $this->get($current);
            
            // if error in table - exit
            if (!$feed) {
                return null;
            }
            
            // store the name of the previous group
            $result[$current] = $feed['name'];
            
            $current = $feed['group_id'];
            
            // if circular - exit
            if (@$result[$current]) {
                return null;
            }
        }
        if ($feed['id'] != self::ROOT_GROUP_ID) {
            return null;
        }
        
        return array_reverse($result, true);
    }

    public function calculatePath($feed)
    {
        
        if (!$feed->group_id) {
            return $feed->id;
        }
        
        $group = $this->getGroup($feed->group_id);
        
        // no parent, return current
        if (!$group) {
            return $feed->id;
        }
        
        return $group['path'] . ',' . $feed->id;
    
    }

    /**
     * @var groupId int
     */
    public function listChildren($groupId, $where = array())
    {
        if (!$this->getGroup($groupId)) {
            return null;
        }
        return $this->fetchAll(array('group_id = ?' => $groupId) + $where)->toArray();
    }

    /**
     * @var groupId int
     */
    public function listFeeds($groupId)
    {
        return $this->listChildren($groupId, array('type = ?' => 'feed'));
    }

    /**
     * @var groupId int
     */
    public function listGroups($groupId)
    {
        return $this->listChildren($groupId, array('type = ?' => 'group'));
    }

    /**
     * return feeds cache handler
     * @return Zend_Cache_Core
     */
	private function getFeedsCache() {
		try {
			$feedsCache = Zend_Registry::get('feedscache');
		} catch (Zend_Exception $e) {
			$cfg = new Zend_Config_Ini('../config/zpfeeds.ini','setup');
			$feedsCache = Zend_Cache::factory('Core', 'File', array('lifetime' => $cfg->cache_limit_sec, 'automatic_serialization' => true, 'cache_id_prefix'=>'zpfeedscache_'),array('cache_dir' => '../cache/'));
			Zend_Registry::set('feedscache', $feedsCache);
		}
		return $feedsCache;
	}
	
    /**
     * Reads the content of the RSS feed and cache it in the database
     * @return Zend_Feed_Abstract - the feed object
     * @throws FeedReadException in case of failure
     */
    public function read($feedId)
    {
        $rowSet = $this->find($feedId);
        if (!count($rowSet)) {
            throw new FeedReadException("Feed id $feedId not found in db.");        	
        }
               
        $row = $rowSet->current();
        $cache = $this->getFeedsCache();
        if( !$xml = $cache->load($feedId)) {
            $xml = file_get_contents($row->url);
            if(!$xml){
        		throw new FeedReadException("Failed retrieving content for feed " . $row->url);        		
        	}            
            
            $date = new Zend_Date();
            $row->updated = $date->get(Zend_Date::W3C);
            $cache->save($xml,$feedId);
        }
        try {
			$feed = Zend_Feed::importString($xml);
	    } catch (Zend_Exception $e){
	    	return null;
		}
        return $feed;
    }
}
?>

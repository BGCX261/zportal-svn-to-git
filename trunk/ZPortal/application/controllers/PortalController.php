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

include_once 'application/models/Feeds.php';
include_once 'application/models/Portal.php';
include_once 'application/controllers/ZPortalController_Abstract.php';

/**
 * PortalController
 * 
 * @author
 * @version 
 */
class PortalController extends ZPortalController_Abstract
{

    /**
     * @var Feeds
     */
    protected $feeds;

    /**
     * @var Portal
     */
    protected $portal;

    /**
     * @see Zend_Controller_Action::init()
     *
     */
    public function init()
    {
        $this->feeds = new Feeds();
        $this->portal = new Portal($this->feeds);
        $this->view->baseUrl = $this->getRequest()->getBaseUrl();        
    }

    /**
     * The private portal view
     */
    public function viewAction()
    {
        $user = $this->authenticate();
        $visibleFeeds = $this->portal->getSubscriptionsScheme($user['id']);
        
        $this->view->feeds = $visibleFeeds;
        $this->view->user = $user;
    }

    /**
     * The private portal view
     */
    public function publicAction()
    {
    	// user information
    	if ( Zend_Auth::getInstance()->hasIdentity()) {
    		$user = Zend_Auth::getInstance()->getIdentity();
    	} else {
    		$user = null;
    	}
    	$this->view->user = $user;
    	
    	// get last newsletter
		$zpCache = Zend_Registry::get('daycache');
		    	
    	if( !$content = $zpCache->load('newsletter') ) {
    		// get feed url
	    	$zpCfgFeeds = new Zend_Config_Ini('../config/zpfeeds.ini','public');    	
	    	$url =$zpCfgFeeds->zend_newsletter;
	    	try {
	    		$feed = Zend_Feed::import($url);
	    		// take the first feed from feed item
		    	foreach ($feed as $entry) {     
		    		$content = $entry->content();
		    		$zpCache->save($content,'newsletter');
		    		break;
		    	}    	
	    	} catch (Exception $e) {
	    		$content = null;
	    	}	    	
    	} 		
    	$this->view->entry = $content;    	
    }
        
    
    public function readAction()
    {
        $user = $this->authenticate();
        
        // group view
        $feedId = (int) $this->getRequest()->getParam('feedId');
        if (!$feedId) {
            $feedId = Feeds::ROOT_GROUP_ID;
        }
        $currentFeed = $this->feeds->get($feedId);
        if (!$currentFeed) {
            return;
        }
        $currentFeed = $this->getSubscriptions($user['id'], $currentFeed, false);
        $currentFeed = $this->getAllEntries($currentFeed);
        $this->view->feed = $currentFeed;
    }

    /**
     * @return array - the feed with all the items sorted
     */
    private function getAllEntries($feed)
    {
        // collect children messages: (including group handling)
        $feed['items'] = array();
        foreach ($feed['subscriptions'] as &$subscription) {
            try {
                $data = $subscription['data'] = $this->feeds->read($subscription['id']);
                if (is_string($data)) { // error occured
                    $timestamp = time();
                    if (!isset($feed['items'][$timestamp])) {
                        $feed['items'][$timestamp] = array();
                    }
                    $feed['items'][$timestamp] = $data;
                    continue;
                }
                /* @var $data Zend_Feed_Abstract */
                foreach ($data as $item) {
                    @$item->realLink = $item->link();
                    /* @var $item Zend_Feed_Entry_Abstract */
                    $item->parentName = $subscription['name'];
                    if ($item instanceof Zend_Feed_Entry_Atom) {
                        $date = $item->updated();
                        if (!$date) {
                            $date = $item->published();
                        }
                        if (!$item->realLink()) {
                            $item->realLink = $item->link('alternate');
                        }
                    } else if ($item instanceof Zend_Feed_Entry_Rss) {
                        $date = $item->pubDate();
                        if (!$date) {
                            $date = $data->pubDate() ? $data->pubDate() : date("ddMMyyyy");
                        }
                    }
                    if (!$date) {
                        continue;
                    }
                    $timestamp = strtotime($date);
                    $item->date = $timestamp ? $this->getPrintableDate($timestamp) : '';
                    // using "multi map" with dates as keys to sort the items further
                    // each entry in the map is an array, since there may be more than one item with the same date
                    if (!isset($feed['items'][$timestamp])) {
                        $feed['items'][$timestamp] = array();
                    }
                    $feed['items'][$timestamp][] = $item;
                }
            } catch (FeedReadException $e) {
                $subscription['data'] = "Feed error: " . $e->getMessage();
            }
        }
        
        // purging unneeded data
        unset($feed['subscriptions']);
        
        // sorting the gathered items
        krsort($feed['items']);
        return $feed;
    }

    private function getPrintableDate($timestamp)
    {
        $date = new Zend_Date($timestamp);
        return $date->get(Zend_Date::DATE_MEDIUM) . ' ' . $date->get(Zend_Date::TIME_MEDIUM);
    }

    /**
     * Get the subscriptions for this user for a specific feed/group 
     */
    private function getSubscriptions($userId, $feed, $forChildren = true)
    {
        $subscribedFeeds = $this->portal->getSubscriptions($userId);
        
        // collect feeds/groups to display
        $visibleFeeds = array();
        
        if ($feed['type'] == 'group') {
            $children = $forChildren ? $this->feeds->listChildren($feed['id']) : array($feed);
            foreach ($children as $childFeed) {
                $childFeed['subscriptions'] = array();
                foreach ($subscribedFeeds as $subscribedFeed) {
                    $subscriptionPath = explode(',', $subscribedFeed['path']);
                    if (in_array($childFeed['id'], $subscriptionPath)) {
                        $childFeed['subscriptions'][] = $subscribedFeed;
                        // TODO also should handle group subscription
                        $visibleFeeds[$childFeed['id']] = $childFeed;
                    }
                }
            }
        } else {
            $feed['subscriptions'] = array($feed);
            $visibleFeeds[$feed['id']] = $feed;
        }
        return $forChildren ? $visibleFeeds : $visibleFeeds[$feed['id']];
    }

    /**
     * The subscribe view and processor
     */
    public function unsubscribeAction()
    {
        $identity = $this->authenticate();
        $userId = $identity['id'];
        $feedId = $this->getRequest()->getParam('feedId');

        // delete if possible
        $deleted = $feedId ? $this->portal->delete("user_id = $userId and feed_id = $feedId") : 0;
            
        $this->view->status = $deleted;        
    }    
    
    /**
     * The subscribe view and processor
     */
    public function subscribeAction()
    {
        $identity = $this->authenticate();
        $userId = $identity['id'];
        
        if ($this->getRequest()->getParam('subscribe')) {
            // subscribe with id parameter
            $feedId = $this->getRequest()->getParam('feedId');
            $feed = $this->feeds->get($feedId);
            // if feed exists and user can register
            if ($feed && !$this->portal->get($userId, $feedId)) {
                $row = $this->portal->createRow(array('user_id' => $userId , 'feed_id' => $feedId));
                $row->save();
            }
            
            if ($this->getRequest()->isXmlHttpRequest()) {
                if ($row) {
                    $this->view->status = "Added";
                } else { // error
                    $this->view->status = "Failed";
                }
                return;
            } else {
                $this->_helper->getHelper('Redirector')->goto('subscribe', null, null, array('feedId' => $feed['group_id'] , 'subscribe' => true));
            }
        }
        
        $groupId = (int) $this->getRequest()->getParam('feedId');
        if (!$groupId) {
            $groupId = Feeds::ROOT_GROUP_ID;
        }
        
        // get all groups
        $this->view->groups = $this->feeds->listGroups($groupId);
        if ($this->view->groups === null) {
            // TODO: handle error - problems in group            
            $this->gotoDefault();
        }
        
        // get predessecors
        $this->view->parents = $this->feeds->listParents($groupId);
        if ($this->view->parents === null) {
            // TODO: handle error - cyclic parents             
            $this->gotoDefault();
        }
        
        // get feeds
        $this->view->feeds = $this->feeds->listFeeds($groupId);
        
        // check if already subscribed
        $this->view->subscription = array();
        foreach ($this->view->feeds as $feed) {
            $id = $feed['id'];
            $this->view->subscription[$id] = $this->portal->get($userId, $id) === null;
        }
        
        // user's identity
        $this->view->user = $identity;
        // the current group
        $this->view->groupId = $groupId; 
    }

    public function group_readAction()
    {
        $user = $this->authenticate();
        
        // group view
        $feedId = (int) $this->getRequest()->getParam('feedId');
        if (!$feedId) {
            $feedId = Feeds::ROOT_GROUP_ID;
        }
        $currentFeed = $this->feeds->get($feedId);
        $currentFeed = $this->getSubscriptions($user['id'], $currentFeed, false);
        $currentFeed = $this->getAllEntries($currentFeed);
        $this->view->feed = $currentFeed;
    }
    
    /**
     * The default action - show the home page
     */
    public function groupAction()
    {
        $user = $this->authenticate();
        
        // group view
        $feedId = (int) $this->getRequest()->getParam('feedId');
        if (!$feedId) {
            $feedId = Feeds::ROOT_GROUP_ID;
        }
        $currentFeed = $this->feeds->get($feedId);
        
        // get predessecors
        $this->view->parents = $this->feeds->listParents($feedId);
        if ($this->view->parents === null) {
            $this->view->parents = array(); // XXX error!
        }
        $this->view->feed = $currentFeed;
        $visibleFeeds = $this->getSubscriptions($user['id'], $currentFeed);
        
        $this->view->feeds = $visibleFeeds;
        $this->view->user = $user;
    }
}
		

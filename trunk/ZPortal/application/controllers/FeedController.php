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
include_once 'Zend/Filter/Input.php';
include_once 'application/controllers/ZPortalController_Abstract.php';
include_once 'application/models/Feeds.php';

/**
 * FeedController
 * 
 * @author
 * @version 
 */
class FeedController extends ZPortalController_Abstract
{

    /**
     * @var Feeds
     */
    private $feeds;

    public function init()
    {
        parent::init();
        $this->feeds = new Feeds();
    }

    public function addAction()
    {
        $this->showForm(true);
    }

    public function editAction()
    {
        $this->showForm(false);
    }

    protected function showForm($isAdd)
    {
        $user = $this->authenticate();
        $isGroup = $this->_request->type == 'group';
        if (!$this->_request->type) {
            $this->_request->setParam('type', 'feed');
        }
        $this->view->isNew = $isAdd;
        $this->view->type = $this->_request->type;
        if (!$isAdd) {
            if (!$this->_request->id || !($feed = $this->feeds->get($this->_request->id))) {
                return $this->_helper->getHelper('Redirector')->goto('add');
            }
            $this->view->assign($feed);
        }
        if ($this->_request->isPost()) {
            $this->processForm($user['id'], $isAdd);
        }
        $this->view->groups = $this->feeds->getGroupNames();
        $this->_helper->getHelper('ViewRenderer')->render('form');
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);
    }

    protected function processForm($userId, $isNew)
    {
        
        // store the data and go to view
        $data = $this->_request->getParams();
        if ($isNew) {
            $data['owner_id'] = $userId;
            $data['update_frequency'] = 0; // FIXME a bug, null should be possible!
            unset($data['id']);
        }
        $result = $this->feeds->updateFeed($data);
        if ($result instanceof Zend_Filter_Input) {
            // Validation failed!
            $this->view->message = $result->getMessages();
            $this->view->assign($data);
        } else {
            // ok
            return $this->_helper->getHelper('Redirector')->goto('subscribe', 'portal', null, array('feedId' => $data['group_id']));
        }
    }

    public function viewAction()
    {
        if (!$this->_request->id) {
            $this->_helper->getHelper('Redirector')->goto('view', 'portal');
            return;
        }
        $this->view->id = $this->_request->id;
        $this->view->feed = $this->feeds->get($this->view->id);
        $this->view->group = $this->feeds->getGroup($this->view->feed['group_id']);
    }
}
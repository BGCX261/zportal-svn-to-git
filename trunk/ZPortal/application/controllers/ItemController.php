<?php

/**
 * ItemController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
include_once 'application/controllers/ZPortalController_Abstract.php';
require_once 'application/models/Items.php';


class ItemController extends ZPortalController_Abstract {
	/**
	 * The default action - show the home page
	 */
	public function addAction() {		
		$arUser = $this->authenticate();
		if( !$this->_request->isPost()) {
			$this->view->channel_id = $this->_getParam('channel_id');
			$this->view->formAction = $this->getRequest()->getBaseUrl().'/item/add';
		} else {
			$channelID = (int)$this->_getParam('channel_id');
			$item = new Items();			
			$item->addItem($channelID, $this->_getParam('title'), $arUser['id']);
			$this->_helper->redirector->goto('add', 'item', null, array('channel_id'=>$channelID));
		}		
	}

}

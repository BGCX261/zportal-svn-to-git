<?php

/**
 * ChannelController
 * 
 * @author
 * @version 
 */

require_once 'application/models/Channels.php';
require_once 'Zend/Controller/Action.php';
include_once 'application/controllers/ZPortalController_Abstract.php';
require_once 'Zend/Controller/Request/Abstract.php';



class ChannelController extends ZPortalController_Abstract {
	
	public function addAction() {	// TODO Auto-generated ChannelController::indexAction() default action
		$arUser = $this->authenticate();
		if( !$this->_request->isPost()) {
			return;
		} 				
		$channels = new Channels();
		$channelID = $channels->addChannel($this->getRequest()->getParam('title'), $arUser['id']); 
		if( $channelID !== false)  {
			$this->_helper->redirector->goto('add', 'item', null, array('channel_id'=>$channelID));								
		}
	}	
	public function modifyAction() {	
	}	
	
}

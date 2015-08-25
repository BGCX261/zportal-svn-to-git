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

include_once 'application/controllers/ZPortalController_Abstract.php';
include_once 'application/models/Portal.php';
include_once 'Zend/Mail/Transport/Smtp.php';
include_once 'Zend/Mail.php';
include_once 'application/models/UserRow.php';
include_once 'application/models/Users.php';
include_once 'Zend/Auth.php';
include_once 'Zend/Filter/Input.php';
require_once "Zend/Controller/Action.php";

/**
 * UserController
 */
class UserController extends ZPortalController_Abstract
{

    /**
     * @var Users
     */
    private $users;

    public function init()
    {
        $this->users = new Users();
    }

    /**
     * The default action - show the registration form
     */
    public function adminAction()
    {
        $this->authenticate();
        
        // check if admin
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity['role'] != 'admin') {
            $this->_helper->getHelper('Redirector')->goto('view', 'portal');
        }
        
        if ($this->getRequest()->isPost()) {
            // submitting form
            $data = $this->_request->getParams();
            
            // update the user  
            try {
                $this->users->updateUser($data);
            } catch (Exception $e) {
                $this->view->message = $e->getMessage();
            }
        }
        
        // show form 
        $this->view->roles = UserRow::getRoles();
    }
}
		

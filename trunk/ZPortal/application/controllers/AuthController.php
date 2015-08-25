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

include_once 'application/models/AuthUserAdapter.php';
include_once 'Zend/Session/Namespace.php';
include_once 'Zend/Auth.php';
require_once "Zend/Controller/Action.php";

/**
 * AuthController - 
 */
class AuthController extends Zend_Controller_Action
{

    const SESSION_NAMESPACE = 'Authentication';

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->goAhead();
    }

    public function loginAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // if the user entered login page while being logged in already, we redirect him to the portal view            $this->goAhead();
            return;
        }
        if ($this->_request->isPost()) {
            $auth = Zend_Auth::getInstance();
            // Set up the authentication adapter
            $authAdapter = new AuthUserAdapter($this->_request->name, $this->_request->password);
            
            // Attempt authentication, saving the result
            $result = $auth->authenticate($authAdapter);
            
            if ($result->isValid()) {
                $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
                $session->unsetAll();                
                $this->goAhead();
                return;
            }
            
            $this->view->message = array_pop($result->getMessages());
            $this->view->name = $this->_request->name;
        }
    }

    /**
     * redirectes to the following page according to session data
     * as a default redirects to portal
     */
    private function goAhead()
    {
        $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        $pathInfo = $session->pathInfo;
        if (is_null($pathInfo)) {
            // if previous location stored, redirect back
            $session->unsetAll();
            $this->_redirect($pathInfo);
            return;
        }
        // else go to portal        $this->_helper->getHelper('Redirector')->goto('view', 'portal');
    }
}


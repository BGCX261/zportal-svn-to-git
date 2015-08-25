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
include_once 'application/library/AjaxableRedirector.php';
include_once 'Zend/Controller/Request/Abstract.php';
include_once 'Zend/Controller/Response/Abstract.php';
include_once 'application/library/AjaxableViewRenderer.php';
include_once 'application/controllers/AuthController.php';
include_once 'Zend/Controller/Action/HelperBroker.php';
include_once 'Zend/Session/Namespace.php';
include_once 'Zend/Controller/Action/Helper/Abstract.php';
include_once 'Zend/Auth.php';
require_once "Zend/Controller/Action.php";

/**
 * ZPortalController_Abstract - Base class for all ZPortal controllers
 * enables:
 *  1. authantication
 *  2. registration of validation and filters
 */
abstract class ZPortalController_Abstract extends Zend_Controller_Action
{
	public function init() {
        // TODO
        // Zend_Controller_Action_HelperBroker::addHelper(new AjaxableViewRenderer($this));
        // Zend_Controller_Action_HelperBroker::addHelper(new AjaxableRedirector($this));		
	}	

    public function preDispatch()
    {
        $session = new Zend_Session_Namespace(AuthController::SESSION_NAMESPACE);
        $session->pathInfo = $this->_request->getPathInfo() . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    public function loginRedirect()
    {
        $this->_helper->getHelper('Redirector')->goto('public', 'portal');
    }

    public function gotoDefault()
    {
        $this->_helper->getHelper('Redirector')->goto('public', 'portal');
    }

    public function __call($name, array $arguments = array())
    {
        $this->gotoDefault();
    }

    protected function authenticate()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->loginRedirect();
            return null;
        }
        return Zend_Auth::getInstance()->getIdentity();
    }

    protected function getIdentity()
    {
        return Zend_Auth::getInstance()->getIdentity();
    }
}
?>
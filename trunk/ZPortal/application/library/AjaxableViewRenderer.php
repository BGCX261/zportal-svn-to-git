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

include_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
include_once 'Zend/Json.php';

class AjaxableViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{

    function __construct(Zend_Controller_Action $controller)
    {
        parent::__construct();
        $this->setActionController($controller);
    }

    function getName()
    {
        return 'ViewRenderer';
    }

    function postDispatch()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] || $this->getRequest()->ajax) {
            $this->getResponse()->appendBody(Zend_Json::encode(var_export($this->view), true));
        } else {
            parent::postDispatch();
        }
    }

}

?>
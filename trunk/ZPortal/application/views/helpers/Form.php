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

include_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_Form extends Zend_View_Helper_FormElement
{
    /* @return Zend_View_Helper_Form */
    public function form($name, $action, $method, array $attribs = array())
    {
        $info = $this->_getInfo($name, $action, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        $this->html = '<form';
        if ($name) {
            $this->html .= ' name="' . $this->view->escape($name) . '"';
        }
        if ($id) {
            $this->html .= ' id="' . $this->view->escape($id) . '"';
        }
        if ($action) {
            $this->html .= ' action="' . $this->view->escape($action) . '"';
        }
        if ($method) {
            $this->html .= ' method="' . $this->view->escape($method) . '"';
        }
        if ($attribs) {
            $this->html .= ' ' . $this->_htmlAttribs($attribs);
        }
        $this->html .= '>';
        return $this;
    }
    public function open()
    {
        return $this->html;
    }
    public function close()
    {
        return '</form>';
    }
}
?>
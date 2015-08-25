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


include_once 'application/views/helpers/Form.php';

class Zend_View_Helper_AjaxableForm extends Zend_View_Helper_Form
{
    /* @return Zend_View_Helper_Form */
    public function ajaxableForm ($name, $action, $method, array $attribs = array())
    {
        parent::form($name, $action, $method, $attribs);
        $this->html .= "\n". $this->startAjax();
        $script=<<<END
<script>
AjaxForm.convert($('$name'));
</script>
END;
        $this->html .= "\n" . $script;
        return $this;
    }
    protected function getJsRoot() {
        return dirname($this->view->url(array(), null, true)) . '/js'; 
    }
    private function startAjax() {
        if(!$this->view->ajaxStarted) {
        $script="
<script src=\"".$this->getJsRoot()."/prototype.js\" />
<script src=\"".$this->getJsRoot()."/ajaxForm.js\" />
";
            $this->view->ajaxStarted = true;
            return $script;
        }
        return '';
    }
    
    public function open ()
    {
        return $this->html;
    }
    public function close ()
    {
        return '</form>';
    }
}
?>
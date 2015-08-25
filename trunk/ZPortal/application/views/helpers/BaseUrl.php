<?php
include_once 'Zend/View/Helper/Url.php';

class Zend_View_Helper_BaseUrl extends Zend_View_Helper_Url
{

    public function baseUrl($up = true)
    {
        $baseUrl = $this->url(array(), null, true);
        if ($up) {
            $baseUrl = dirname($baseUrl);
        }
        $baseUrl = str_replace('\\', '/', $baseUrl);
        if ($baseUrl{strlen($baseUrl) - 1} == '/') {
            $baseUrl = substr($baseUrl, 0, -1);
        }
        return $baseUrl;
    }
}

?>
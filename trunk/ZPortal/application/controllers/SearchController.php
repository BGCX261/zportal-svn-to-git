<?php

/**
 * SearchController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Search/Lucene.php';
require_once 'Zend/Controller/Action.php';

class SearchController extends Zend_Controller_Action
{
    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $query = $this->getRequest()->getParam('q');
        if ($query) {
            $index = Zend_Search_Lucene::open('../lucene/index');
            $hits = $index->find($query);
            $this->view->query = $query;
            if (! empty($hits)) {
                $this->view->results = $hits;
            }
        }
    }

}
?>


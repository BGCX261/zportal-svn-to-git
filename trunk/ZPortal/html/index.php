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

// set_include_path(get_include_path() . PATH_SEPARATOR . 'C:/Program Files/Zend/Zwas/ZendFramework/library' );
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)));
set_include_path(get_include_path() . PATH_SEPARATOR . 'application/library');

include_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Controller/Front.php';
include_once 'Zend/Config/Ini.php';
include_once 'Zend/Db.php';
include_once 'Zend/Registry.php';
require_once 'Zend/Cache.php';


/**
 * Setup controller
 */
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory('../application/controllers');
$controller->throwExceptions(false); // should be turned off in production server


// load DB configuration
$config = new Zend_Config_Ini('../config/zportal.ini');
$controller->setBaseUrl($config->setup->baseurl);


// init Cache 
$zpCache =  Zend_Cache::factory('Core', 'File', array('lifetime' => 86400, 'automatic_serialization' => true, 'cache_id_prefix'=>'zpdaycache_'),array('cache_dir' => '../cache/'));
Zend_Registry::set('daycache', $zpCache);

// create DB connection
$db = Zend_DB::factory($config->database->adapter, $config->database->params->toArray());
/* @var $db Zend_DB_Adapter_Abstract */

// Store database handler as default adapter
Zend_Db_Table_Abstract::setDefaultAdapter($db);

// run!
$controller->dispatch();

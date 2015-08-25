<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)));
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/zend/share/ZendFramework/library');

// Load some components
require_once 'Zend/Search/Lucene.php';
require_once 'Zend/Log.php';
require_once 'Zend/Date.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Log/Writer/Stream.php';
require_once 'Zend/Db.php';
require_once 'Zend/Config/Ini.php';
require_once 'application/models/Feeds.php';
require_once 'Zend/Db/Table/Abstract.php';
require_once 'Rss.php';

// Define some constants
define('APP_ROOT', realpath(dirname(dirname(__FILE__))));
define('CACHE_FOLDER', '../cache');

// Set up log
$filename = APP_ROOT . DIRECTORY_SEPARATOR . 'lucene' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'crawler.log';
$writer = new Zend_Log_Writer_Stream($filename);
$log = new Zend_Log($writer);
$log->info('Crawler starting up');


// Open index
$indexpath = APP_ROOT . DIRECTORY_SEPARATOR . 'lucene' . DIRECTORY_SEPARATOR . 'index';

try {
    $index = Zend_Search_Lucene::create($indexpath);
    $log->info("Created new index in $indexpath");
    // If both fail, give up and show error message
} catch (Zend_Search_Lucene_Exception $e) {
    $log->info("Failed opening or creating index in $indexpath");
    $log->info($e->getMessage());
    echo "Unable to open or create index: {$e->getMessage()}";
    exit(1);
}

$log->info('Crawler loads db');

// load DB configuration
$config = new Zend_Config_Ini('../config/zportal.ini', 'database');
Zend_Registry::set('config', $config);

// create DB connection
$db = Zend_DB::factory($config->adapter, $config->params->toArray());
/* @var $db Zend_DB_Adapter_Abstract */

// Store database handler as default adapter
Zend_Db_Table_Abstract::setDefaultAdapter($db);

$table = new Feeds();
$list = $table->fetchAll();
$targets = 0;

foreach ($list as $item) {
    if ($item->type != 'feed') {
        continue;
    }

    $log->info("Fetched " . $item->url);

    $xml = file_get_contents($item->url);
    if(!$xml){
        $log->info("Error fetching " . $item->url);
        continue;
    }
    // in case the update frequency is not specified, set a default of 5 minutes
    $date = new Zend_Date();
    $item->updated = $date->get(Zend_Date::W3C);
    // write to file
    @unlink($item->xml);
    $filename = '../cache/' . $item->name . $date->toString('ddMMyyHHmmss');
    @file_put_contents($filename, $xml);
    $item->xml = $filename;
    $item->save();

    // Fetch content with HTTP Client
    $log->info("Reading " . $filename);
    // Create document
    $doc = Zend_Search_Lucene_Feed::loadFeedFile($item->id,  $item->url, $filename);
    // Index
    $index->addDocument($doc);
    $log->info("Indexed $filename");
    $targets++;

}

$log->info("Iterated over " . $targets . " documents");
$log->info("Optimizing index...");
$index->optimize();
$index->commit();
$log->info("Done. Index now contains " . $index->numDocs() . " documents");
$log->info("Crawler shutting down");

?>
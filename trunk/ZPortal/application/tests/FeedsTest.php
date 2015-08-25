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

include_once 'Zend/Registry.php';
include_once 'application/tests/mocks/DbMock.php';
require_once ('application/models/Feeds.php');

/**
 * Feeds test case.
 */
class FeedsTest extends PHPUnit_Framework_TestCase
{

    /**
     * Old default db
     */
    public $oldDefaultDb;

    /**
     * Constructs the test case.
     */
    public function __construct()
    {    

    // TODO Auto-generated constructor
    

    }

    /**
     * @var Feeds
     */
    private $Feeds;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // save the old db
        $this->oldDefaultDb = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        // set a mock up (default) db 
        $db = new DbMock();
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        
        $this->Feeds = new Feeds();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Feeds = null;
        
        // restore the old default db
        Zend_Db_Table_Abstract::setDefaultAdapter($this->oldDefaultDb);
        parent::tearDown();
    }

    /**
     * Tests Feeds->AddFeed()
     */
    public function testAddFeedSuccess()
    {
        // TODO Auto-generated FeedsTest->testAddFeed()
        $this->markTestIncomplete("AddFeed test not implemented");
        
        $result = $this->Feeds->addEditFeed(array('name' => 'Test Feed' , 'description' => 'This feed is added for testing purposes' , 'url' => 'http://www.zend.com/rss' , // should be a real feed!
'type' => 'feed' , 'group_id' => 1)// root group
);
        if ($result instanceof Zend_Filter_Input) {
            $this->fail(implode(', ', $result->getErrors()));
        }
    }

    /**
     * Tests Feeds->Get()
     */
    public function testGet()
    {
        // TODO Auto-generated FeedsTest->testGet()
        $this->markTestIncomplete("Get test not implemented");
        
        $this->Feeds->Get(/* parameters */);
    
    }

    /**
     * Tests Feeds->GetFeed()
     */
    public function testGetFeed()
    {
        // TODO Auto-generated FeedsTest->testGetFeed()
        $this->markTestIncomplete("GetFeed test not implemented");
        
        $this->Feeds->GetFeed(/* parameters */);
    
    }

    /**
     * Tests Feeds->GetGroup()
     */
    public function testGetGroup()
    {
        // TODO Auto-generated FeedsTest->testGetGroup()
        $this->markTestIncomplete("GetGroup test not implemented");
        
        $this->Feeds->GetGroup(/* parameters */);
    
    }

    /**
     * Tests Feeds->GetGroups()
     */
    public function testGetGroups()
    {
        // TODO Auto-generated FeedsTest->testGetGroups()
        $this->markTestIncomplete("GetGroups test not implemented");
        
        $this->Feeds->GetGroups(/* parameters */);
    
    }

    /**
     * Tests Feeds->UrlIsDuplicate()
     */
    public function testUrlIsDuplicate()
    {
        // TODO Auto-generated FeedsTest->testUrlIsDuplicate()
        $this->markTestIncomplete("UrlIsDuplicate test not implemented");
        
        $this->Feeds->UrlIsDuplicate(/* parameters */);
    
    }

}


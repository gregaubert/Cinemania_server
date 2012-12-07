<?php

define('TEST_BASE_URL','http://localhost/cinemania/app/test.php/');
 
class DevicesTest extends CWebTestCase
{
    public $fixtures=array(
        'devices'=>'Device',
        'games'=>'Game',
    );
    
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/home/jorge/Aptana\ Studio\ 3\ Workspace/screenshots';
    protected $screenshotUrl = 'http://localhost/projects/screenshots';
    
    /**
     * Sets up before each test method runs.
     * This mainly sets the base URL for the test application.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setBrowser('firefox');
        $this->setBrowserUrl(TEST_BASE_URL);
    }
 
    public function testRegister()
    {
      $this->open('devices/register');
      
      // verify the sample post title exists
      $this->assertTextPresent("ERROR: missing information");
    }
  
}

?>
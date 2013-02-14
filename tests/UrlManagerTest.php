<?php

require_once __DIR__.'/../flight/util/UrlManager.php';
use flight\util\UrlManager;


/**
 * To use this test in phpStorm - remove "namespace" string from class under testing and "use" string in test file.
 *
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-02-14 at 11:01:56.
 */
class UrlManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlManager
     */
    protected $object;

    /**
     * @var routes
     */
    protected $routes;

    /**
     * @var routes
     */
    protected $compareAbsoluteUrl;


    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $_SERVER['HTTP_HOST'] = 'hidemyass.dev';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['DOCUMENT_ROOT'] = 'D:/server/hidemyass.dev';

        $this->compareAbsoluteUrl = "http://hidemyass.dev";

        $this->routes = array(
            '/' => array('\controllers\Index', 'start'),
            '/proxy' => array('\controllers\ProxyController', 'start'),
            '/session/@var:[a-z]*' => array('\controllers\Test','session'),
            '/@name/@id:[0-9]+' => array('\controllers\Test','test'),
            '/@name:[a-z]+/@action:[a-z]+/@id:[0-9]+' => array('\controllers\Test','testing'),
            '/@name/@action:[a-z]+' => array('\controllers\Test','create'),
        );
        $this->object = new UrlManager($this->routes);
    }

    /**
     * Tears down the fixture.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers flight\util\UrlManager::createUrl
     */
    public function testCreateUrl()
    {
        $indexUrl = $this->object->createUrl('index/start');
        $testUrl  = $this->object->createUrl('test/test', array('name' => 'test', 'id' => 1));

        $this->assertEquals($this->object->getAbsoluteUrl() . '/', $indexUrl);
        $this->assertEquals($this->object->getAbsoluteUrl() . '/test/1', $testUrl);
    }

    /**
     * @covers flight\util\UrlManager::getAbsoluteUrl
     */
    public function testGetAbsoluteUrl()
    {
        $this->assertEquals($this->object->getAbsoluteUrl(), $this->compareAbsoluteUrl);
    }
}

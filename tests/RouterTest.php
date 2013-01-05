<?php
/**
 * Flight: An extensible micro-framework.
 *
 * @copyright   Copyright (c) 2011, Mike Cao <mike@mikecao.com>
 * @license     http://www.opensource.org/licenses/mit-license.php
 */

require_once 'PHPUnit.php';
require_once __DIR__.'/../flight/net/Router.php';
require_once __DIR__.'/../flight/net/Request.php';

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \flight\net\Router
     */
    private $router;

    /**
     * @var \flight\net\Request
     */
    private $request;
    
    function setUp(){
        Flight::init();
        $this->router = new \flight\net\Router();
        $this->request = new \flight\net\Request();
    }

    // Checks if a route was matched
    function check($str = 'OK'){
        $callback = $this->router->route($this->request);
        $params = array_values($this->router->params);

        $this->assertTrue(is_callable($callback));

        call_user_func_array($callback, $params);

        $this->expectOutputString($str);
    }

    // Default route
    function testDefaultRoute(){
        $this->router->map('/', 'ok');
        $this->request->url = '/';

        $this->check();
    }

    // Simple path
    function testPathRoute() {
        $this->router->map('/path', 'ok');
        $this->request->url = '/path';

        $this->check();
    }

    // POST route
    function testPostRoute(){
        $this->router->map('POST /', 'ok');
        $this->request->url = '/';
        $this->request->method = 'POST';

        $this->check();
    }

    // Either GET or POST route
    function testGetPostRoute(){
        $this->router->map('GET|POST /', 'ok');
        $this->request->url = '/';
        $this->request->method = 'GET';

        $this->check();
    }

    // Test regular expression matching
    function testRegEx(){
        $this->router->map('/num/[0-9]+', 'ok');
        $this->request->url = '/num/1234';

        $this->check();
    }

    // Passing URL parameters
    function testUrlParameters(){
        $this->router->map('/user/@id', function($id){
            echo $id;
        });
        $this->request->url = '/user/123';

        $this->check('123');
    }

    // Passing URL parameters matched with regular expression
    function testRegExParameters(){
        $this->router->map('/test/@name:[a-z]+', function($name){
            echo $name;
        });

        $this->request->url = '/test/abc';

        $this->check('abc');
    }

    // Optional parameters
    function testOptionalParameters(){
        $this->router->map('/blog(/@year(/@month(/@day)))', function($year, $month, $day){
            echo "$year,$month,$day";
        });

        $this->request->url = '/blog/2000';

        $this->check('2000,,');
    }

    // Wildcard matching
    function testWildcard(){
        $this->router->map('/account/*', 'ok');
        $this->request->url = '/account/123/abc/xyz';

        $this->check();
    }
}

function ok(){
    echo 'OK';
}

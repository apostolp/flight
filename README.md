# What is FlightMVC?

FlightMVC is a fast, simple, extensible framework for PHP with integrated MVC pattern used Flight framework as core.

# Quick Start

First of all, download the source code from GitHub FlightMVC https://github.com/apostolp/flight or pull using Git

# App - MVC - create an application directory structure

 /app

   -- /config

   -- /controllers

   -- /models

   -- /views

 /flight

 /.htaccess

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]

 /index.php
```php
    $app = dirname(__FILE__) . '/app';
    $config = 'main.php';

    require 'flight/Flight.php';

    Flight::start();
```

# app/config

set in root www directory in index.php file
    
```php
    $config = 'main.php';
```

path - app/main.php

example for multiple DB connections

return array(

```php
    'dbFactory' =>
        array(
        'db' => array(
            'class' => 'PDOWrapper',
            'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=cdcol',
            'username' => 'root',
            'password' => '',
            'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
            ),
        'db2' => array(
            'class' => 'PDOWrapper',
            'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=calendar',
            'username' => 'root',
            'password' => '',
            'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
        ),

	),
	);

    $db = Flight::db();

    $db2 = Flight::db2();
```

example config routes

```php
    'routes' =>
        array(
            '/' => array('\controllers\Index', 'start'),
            '/@name/@id:[0-9]+' => array('\controllers\Test','test'),
        ),
```

# example app/controllers - Test.php

```php
    namespace controllers;

    use flight;

    class Test
    {
        public static function test()
        {
            $users = new \models\Users();

       	    Flight::render('test_test.php', array('model' => $users->getResults()));
        }
    }
```

# example app/models - Users.php

```php
    namespace models;

    use flight;

    class Users
    {

        public function getResults()
        {
            $db = Flight::db();
            $results = $db->select("cds");

            return $results;
        }
    }
    ```

# example app/views - test_test.php

    ```php
    print_r($model);
    ```

# Console command support

/cron.php

    ```php
    $app = dirname(__FILE__) . '/app';
    $config = 'main.php';

    require 'flight/Flight.php';

    Flight::console();
    ```


/app/console/Test.php

    ```php
    namespace console;

    use flight;

    class Test
    {
        public static function run($args)
        {
            var_dump($args);
        }
    }
    ```

RUN: php cron.php test 1 2 3


# Cache on file system supported

path - app/main.php
```php
    'cache' =>
        array(
            'class' => 'FileCache',
            'cache_dir' => 'app/cache',
        ),
        ```

add object to cache
```php
    Flight::cache()->setObject('obj', $obj, 100);
    ```

get object from cache

```php
    Flight::cache()->getObject('obj');
```	


# UrlManager

With UrlManager you can get absolute url or create url with parameters you need.
```php
    Flight::urlManager()->getAbsoluteUrl() - Gets a string.

    Flight::urlManager()->createUrl($route, $params = array()) - Generates url according to specified route.
```

CreateUrl using example:
```php
    Flight::urlManager()->createUrl('ControllerName/action', array('param1' => 'value1', 'param2' => 'value2',));
```
	
	
# Session handler

Configuration parameters:
```php
	return array(    
		'session' =>
			array(
				// path from root of application like '/session/' or empty string - default php.ini path
				'savePath' => '',
				//int or string '24*60*60' or empty string (max session lifetime).
				'lifetime' => '',
				'sessionName' => 'PHPSESSID',
			),
	);
	```

How to use:
```php
	$session = Flight::session();
	$session['key'] = 'value';
	$var = $session['key'];
```

Or other way to use session:
```php
	Flight::$session['key'] = 'value';
	$var = Flight::$session['key'];
```		
		
Public methods:
```php
	$session->open() - open session (open by default).
	$session->close() - close session.
	$session->getID() - return session id.
	$session->getName() - return session name.
	$session->count() - return number of items in session.
	$session->getKeys() - return array with session variable names.
	$session->remove('key') - return the removed value, null if no such session variable.
	$session->clear() - remove all session variables.
	$session->contains('key') - return boolean.
```

# Type hinting in PHPStorm

add autocomplete for DB PDOWrapper
```php
    /**
    * @var flight\util\PDOWrapper $db
    */
    public $db;

    public function __construct()
    {
        $this->db = Flight::db();
    }
```

# What is Flight?

Flight is a fast, simple, extensible framework for PHP. Flight enables you to quickly and easily build RESTful web applications.

```php
require 'flight/Flight.php';

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();
```

[Learn more](http://flightphp.com/learn)

# Requirements

Flight requires `PHP 5.3` or greater. 

# License

Flight is released under the [MIT](http://www.opensource.org/licenses/mit-license.php) license.

# Installation

1\. [Download](https://github.com/mikecao/flight/tarball/master) and extract the Flight framework files to your web directory.

2\. Configure your webserver.

For *Apache*, edit your `.htaccess` file with the following:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

For *Nginx*, add the following to your server declaration:

```
server {
    location / {
        try_files $uri $uri/ /index.php;
    }
}
```
3\. Create your `index.php` file.

First include the framework.

```php
require 'flight/Flight.php';
```

Then define a route and assign a function to handle the request.

```php
Flight::route('/', function(){
    echo 'hello world!';
});
```

Finally, start the framework.

```php
Flight::start();
```

# Routing 

Routing in Flight is done by matching a URL pattern with a callback function.

```php
Flight::route('/', function(){
    echo 'hello world!';
});
```

The callback can be any object that is callable. So you can use a regular function:

```php
function hello(){
    echo 'hello world!';
}

Flight::route('/', 'hello');
```

Or a class method:

```php
class Greeting {
    public static function hello() {
        echo 'hello world!';
    }
}

Flight::route('/', array('Greeting','hello'));
```

Routes are matched in the order they are defined. The first route to match a request will be invoked.

## Method Routing

By default, route patterns are matched against all request methods. You can respond to specific methods by placing an identifier before the URL.

```php
Flight::route('GET /', function(){
    echo 'I received a GET request.';
});

Flight::route('POST /', function(){
    echo 'I received a POST request.';
});
```

You can also map multiple methods to a single callback by using a `|` delimiter:

```php
Flight::route('GET|POST /', function(){
    echo 'I received either a GET or a POST request.';
});
```

Method specific routes have precedence over global routes.

## Regular Expressions

You can use regular expressions in your routes:

```php
Flight::route('/user/[0-9]+', function(){
    // This will match /user/1234
});
```

## Named Parameters

You can specify named parameters in your routes which will be passed along to your callback function.

```php
Flight::route('/@name/@id', function($name, $id){
    echo "hello, $name ($id)!";
});
```

You can also include regular expressions with your named parameters by using the `:` delimiter:

```php
Flight::route('/@name/@id:[0-9]{3}', function($name, $id){
    // This will match /bob/123
    // But will not match /bob/12345
});
```

## Optional Parameters

You can specify named parameters that are optional for matching by wrapping segments in parentheses.

```php
Flight::route('/blog(/@year(/@month(/@day)))', function($year, $month, $day){
    // This will match the following URLS:
    // /blog/2012/12/10
    // /blog/2012/12
    // /blog/2012
    // /blog
});
```

Any optional parameters that are not matched will be passed in as NULL.

## Wildcards

Matching is only done on individual URL segments. If you want to match multiple segments you can use the `*` wildcard.

```php
Flight::route('/blog/*', function(){
    // This will match /blog/2000/02/01
});
```

To route all requests to a single callback, you can do:

```php
Flight::route('*', function(){
    // Do something
});
```

# Extending

Flight is designed to be an extensible framework. The framework comes with a set of default methods and components, but it allows you to map your own methods, register your own classes, or even override existing classes and methods.

## Mapping Methods

To map your own custom method, you use the `map` function:

```php
// Map your method
Flight::map('hello', function($name){
    echo "hello $name!";
});

// Call your custom method
Flight::hello('Bob');
```

## Registering Classes

To register your own class, you use the `register` function:

```php
// Register your class
Flight::register('user', 'User');

// Get an instance of your class
$user = Flight::user();
```

The register method also allows you to pass along parameters to your class constructor. So when you load your custom class, it will come pre-initialized. You can define the constructor parameters by passing in an additional array. Here's an example of loading a database connection:

```php
// Register class with constructor parameters
Flight::register('db', 'Database', array('localhost','mydb','user','pass'));

// Get an instance of your class
// This will create an object with the defined parameters
//
//     new Database('localhost', 'mydb', 'user', 'pass');
//
$db = Flight::db();
```

If you pass in an additional callback parameter, it will be executed immediately after class construction. This allows you to perform any set up procedures for your new object. The callback function takes one parameter, an instance of the new object.

```php
// The callback will be passed the object that was constructed
Flight::register('db', 'Database', array('localhost', 'mydb', 'user', 'pass'), function($db){
    $db->connect();
});
```

By default, every time you load your class you will get a shared instance.
To get a new instance of a class, simply pass in `false` as a parameter:

```php
// Shared instance of Database class
$shared = Flight::db();

// New instance of Database class
$new = Flight::db(false);
```

Keep in mind that mapped methods have precedence over registered classes. If you declare both using the same name, only the mapped method will be invoked.

# PDO Wrapper based on [Origin wrapper PDO class](http://www.imavex.com/php-pdo-wrapper-class/)

System Requirements: PDO Extension
Appropriate PDO Driver(s) - PDO_SQLITE, PDO_MYSQL, PDO_PGSQL

    // Get an instance of your class
    // This will create an object with the defined parameters in app config see section "App config"

    $db = Flight::db();

# Overriding

Flight allows you to override its default functionality to suit your own needs, without having to modify any code.

For example, when Flight cannot match a URL to a route, it invokes the `notFound` method which sends a generic `HTTP 404` response.
You can override this behavior by using the `map` method:

```php
Flight::map('notFound', function(){
    // Display custom 404 page
    include 'errors/404.html';
});
```

Flight also allows you to replace core components of the framework.
For example you can replace the default Router class with your own custom class:

```php
// Register your custom class
Flight::register('router', 'MyRouter');

// When Flight loads the Router instance, it will load your class
$myrouter = Flight::router();
```

Framework methods like `map` and `register` however cannot be overridden. You will get an error if you try to do so.

# Filtering

Flight allows you to filter methods before and after they are called. There are no predefined hooks you need to memorize. You can filter any of the default framework methods as well as any custom methods that you've mapped.

A filter function looks like this:

```php
function(&$params, &$output) {
    // Filter code
}
```

Using the passed in variables you can manipulate the input parameters and/or the output.

You can have a filter run before a method by doing:

```php
Flight::before('start', function(&$params, &$output){
    // Do something
});
```

You can have a filter run after a method by doing:

```php
Flight::after('start', function(&$params, &$output){
    // Do something
});
```

You can add as many filters as you want to any method. They will be called in the order that they are declared.

Here's an example of the filtering process:

```php
// Map a custom method
Flight::map('hello', function($name){
    return "Hello, $name!";
});

// Add a before filter
Flight::before('hello', function(&$params, &$output){
// Manipulate the parameter
    $params[0] = 'Fred';
});

// Add an after filter
Flight::after('hello', function(&$params, &$output){
// Manipulate the output
    $output .= " Have a nice day!";
}

// Invoke the custom method
echo Flight::hello('Bob');
```

This should display:

    Hello Fred! Have a nice day! 

If you have defined multiple filters, you can break the chain by returning `false` in any of your filter functions:

```php
Flight::before('start', function(&$params, &$output){
    echo 'one';
});

Flight::before('start', function(&$params, &$output){
    echo 'two';

// This will end the chain
    return false;
});

// This will not get called
Flight::before('start', function(&$params, &$output){
    echo 'three';
});
```

Note, core methods such as `map` and `register` cannot be filtered because they are called directly and not invoked dynamically.

# Variables

Flight allows you to save variables so that they can be used anywhere in your application.

```php
// Save your variable
Flight::set('id', 123);

// Elsewhere in your application
$id = Flight::get('id');
```
To see if a variable has been set you can do:

```php
if (Flight::has('id')) {
     // Do something
}
```

You can clear a variable by doing:

```php
// Clears the id variable
Flight::clear('id');

// Clears all variables
Flight::clear();
```

Flight also uses variables for configuration purposes.

```php
Flight::set('flight.log_errors', true);
```

# Views 

Flight provides some basic templating functionality by default. To display a view template call the `render` method with the name of the template file and optional template data:

```php
Flight::render('hello.php', array('name' => 'Bob'));
```

The template data you pass in is automatically injected into the template and can be reference like a local variable. Template files are simply PHP files. If the content of the `hello.php` template file is:

```php
Hello, '<?php echo $name; ?>'!
```

The output would be:

    Hello, Bob!

You can also manually set view variables by using the set method:

```php
Flight::view()->set('name', 'Bob');
```

The variable `name` is now available across all your views. So you can simply do:

```php
Flight::render('hello');
```

Note that when specifying the name of the template in the render method, you can leave out the `.php` extension.

By default Flight will look for a `views` directory for template files. You can set an alternate path for your templates by setting the following config:

```php
Flight::set('flight.views.path', '/path/to/views');
```

## Layouts

It is common for websites to have a single layout template file with interchanging content. To render content to be used in a layout, you can pass in an optional parameter to the `render` method.

```php
Flight::render('header', array('heading' => 'Hello'), 'header_content');
Flight::render('body', array('message' => 'World'), 'body_content');
```

Your view will then have saved variables called `header_content` and `body_content`. You can then render your layout by doing:

```php
Flight::render('layout', array('title' => 'Home Page'));
```

If the template files looks like this:

`header.php`:

```php
<h1><?php echo $heading; ?></h1>
```

`body.php`:

```php
<div><?php echo $body; ?></div>
```

`layout.php`:

```php
<html>
<head>
<title><?php echo $title; ?></title>
</head>
<body>
<?php echo $header_content; ?>
<?php echo $body_content; ?>
</body>
</html>
```

The output would be:
```html
<html>
<head>
<title>Home Page</title>
</head>
<body>
<h1>Hello</h1>
<div>World</div>
</body>
</html>
```

## Custom Views

Flight allows you to swap out the default view engine simply by registering your own view class. Here's how you would use the [Smarty](http://www.smarty.net/) template engine for your views:

```php
// Load Smarty library
require './Smarty/libs/Smarty.class.php';

// Register Smarty as the view class
// Also pass a callback function to configure Smarty on load
Flight::register('view', 'Smarty', array(), function($smarty){
    $smarty->template_dir = './templates/';
    $smarty->compile_dir = './templates_c/';
    $smarty->config_dir = './config/';
    $smarty->cache_dir = './cache/';
});

// Assign template data
Flight::view()->assign('name', 'Bob');

// Display the template
Flight::view()->display('hello.tpl');
```

For completeness, you should also override Flight's default render method:

```php
Flight::map('render', function($template, $data){
    Flight::view()->assign($data);
    Flight::view()->display($template);
});
```
# Error Handling 

## Errors and Exceptions

All errors and exceptions are caught by Flight and passed to the `error` method.
The default behavior is to send a generic `HTTP 500 Internal Server Error` response with some error information.

You can override this behavior for your own needs:

```php
Flight::map('error', function(){
    // Handle error
});
```

By default errors are not logged to the web server. You can enable this by changing the config:

```php
Flight::set('flight.log_errors', true);
```

## Not Found

When a URL can't be found, Flight calls the `notFound` method. The default behavior is to send an `HTTP 404 Not Found` response with a simple message. 

You can override this behavior for your own needs:

```php
Flight::map('notFound', function(){
    // Handle not found
});
```

# Redirects 

You can redirect the current request by using the `redirect` method and passing in a new URL:

```php
Flight::redirect('/new/location');
```

# Requests

Flight encapsulates the HTTP request into a single object, which can be accessed by doing:

```php
$request = Flight::request();
```

The request object provides the following properties:

```
url - The URL being requested
base - The parent subdirectory of the URL
method - The request method (GET, POST, PUT, DELETE)
referrer - The referrer URL
ip - IP address of the client
ajax - Whether the request is an AJAX request
scheme - The server protocol (http, https)
user_agent - Browser information
body - Raw data from the request body
type - The content type
length - The content length
query - Query string parameters
data - Post parameters
cookies - Cookie parameters
files - Uploaded files
```

You can access the `query`, `data`, `cookies`, and `files` properties as arrays or objects.

So, to get a query string parameter, you can do:

```php
$id = Flight::request()->query['id'];
```

Or you can do:

```php
$id = Flight::request()->query->id;
```

# HTTP Caching 

Flight provides built-in support for HTTP level caching. If the caching condition is met, Flight will return an HTTP `304 Not Modified` response. The next time the client requests the same resource, they will be prompted to use their locally cached version.

## Last-Modified

You can use the `lastModified` method and pass in a UNIX timestamp to set the date and time a page was last modified. The client will continue to use their cache until the last modified value is changed.

```php
Flight::route('/news', function(){
    Flight::lastModified(1234567890);
    echo 'This content will be cached.';
});
```

## ETag

`ETag` caching is similar to `Last-Modified`, except you can specify any id you want for the resource:

```php
Flight::route('/news', function(){
    Flight::etag('my-unique-id');
    echo 'This content will be cached.';
});
```

Keep in mind that calling either `lastModified` or `etag` will both set and check the cache value. If the cache value is the same between requests, Flight will immediately send an `HTTP 304` response and stop processing.

# Stopping 

You can stop the framework at any point by calling the `halt` method:

```php
Flight::halt();
```

You can also specify an optional `HTTP` status code and message:

```php
Flight::halt(200, 'Be right back...');
```

Calling `halt` will discard any response content up to that point. If you want to stop the framework and output the current response, use the `stop` method:

```php
Flight::stop();
```

# Framework Methods

Flight is designed to be easy to use and understand. The following is the complete set of methods for the framework. It consists of core methods, which are regular static methods, and extensible methods, which can be filtered or overridden.

## Core Methods

```php
Flight::map($name, $callback) // Creates a custom framework method.
Flight::register($name, $class, [$params], [$callback]) // Registers a class to a framework method.
Flight::before($name, $callback) // Adds a filter before a framework method.
Flight::after($name, $callback) // Adds a filter after a framework method.
Flight::path($path) // Adds a path for autoloading classes.
Flight::get($key) // Gets a variable.
Flight::set($key, $value) // Sets a variable.
Flight::has($key) // Checks if a variable is set.
Flight::clear([$key]) // Clears a variable.
```

## Extensible Methods

```php
Flight::start() // Starts the framework.
Flight::stop() // Stops the framework and sends a response.
Flight::halt([$code], [$message]) // Stop the framework with an optional status code and message.
Flight::route($pattern, $callback) // Maps a URL pattern to a callback.
Flight::redirect($url, [$code]) // Redirects to another URL.
Flight::render($file, [$data], [$key]) // Renders a template file.
Flight::error($exception) // Sends an HTTP 500 response.
Flight::notFound() // Sends an HTTP 400 response.
Flight::etag($id, [$type]) // Performs ETag HTTP caching.
Flight::lastModified($time) // Performs last modified HTTP caching.
Flight::json($data) // Sends a JSON response.
```

    Flight::console() - Starts the framework in console mode.

Any custom methods added with `map` and `register` can also be filtered.

## TODO

- Session handler
- createUrl - creating url 

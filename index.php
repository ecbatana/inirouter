<?php
require('src/Autoload.php');

// Initialize Autoloader Class
$autoload = new Autoload;
$autoload->run();

use Inirouter\Dispatch;
use Inirouter\Dispatcher;
use Inirouter\Route;
use Inirouter\RouteConfig;
use Inirouter\RouteCollection;
use Inirouter\RouteParser;
use Inirouter\Router;

$r = new Router(
     new Route(
     new RouteConfig,
     new RouteCollection,
     new RouteParser,
     new Dispatcher
    )
);
$test = function()
{
    echo 'IniRouter';
};

$r->route(['GET'], '/', $test, true);
$r->route(['GET'], '/test', function() {
    echo 'Coming from /test';
});
$r->route(['GET'], '/[:num]', function($id) {
    echo 'Coming from /[:num] ' . $id;
});
$r->route(['GET'], '/[:str]', function($str) {
    echo 'Coming from /[:str] ' . $str;
});
$r->route(['GET'], '/[:num]/[:num]', function($id, $id2) {
    echo 'Coming from /[:num]/[:num] ' . $id . $id2;
});
$r->route(['GET'], '/[:num]/[:str]', function($id, $str) {
    echo 'Coming from /[:num]/[:str] ' . $id . $str;
});
$r->route(['GET'], '/test/[:str]', function($name) {
    echo 'Coming from /test/[:str] ' . $name;
});
$r->route(['GET'], '/test/[:str]/test', function($name) {
    echo 'initest' . $name;
});
$r->route(['GET'], '/test/[:str]/test/[:num]', function($name, $id) {
    echo 'initest' . $name . '-' . $id;
});
$r->run(); // run to route
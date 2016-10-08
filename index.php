<?php
require('src/Autoload.php');

// Initialize Autoloader Class
$autoload = new Autoload;
$autoload->run();

// call required class
use Inirouter\Dispatch;
use Inirouter\Dispatcher;
use Inirouter\Route;
use Inirouter\RouteConfig;
use Inirouter\RouteCollection;
use Inirouter\RouteParser;
use Inirouter\Router;

// setup
$r = new Router(
     new Route(
     new RouteConfig,
     new RouteCollection,
     new RouteParser,
     new Dispatcher
    )
);

// testing
$r->route(['GET'], '/', function() {
    echo 'Coming from /';
});
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
$r->route(['GET'], '/test/[:num]', function($name) {
    echo 'Coming from /test/[:num] ' . $name;
});
$r->route(['GET'], '/test/[:str]', function($name) {
    echo 'Coming from /test/[:str] ' . $name;
});
$r->route(['GET'], '/test/[:num]/test', function($name) {
    echo 'Coming from /test/[:num]/test' . $name;
});
$r->route(['GET'], '/test/[:str]/test', function($name) {
    echo 'Coming from /test/[:str]/test' . $name;
});
$r->route(['GET'], '/test/[:str]/[:num]', function($name, $id) {
    echo 'Coming from /test/[:str]/[:num]' . $name . $id;
});
$r->route(['GET'], '/test/[:str]/test/[:num]/[:str]/[:num]', function($name, $id, $satu, $dua) {
    echo '/test/[:str]/test/[:num]/[:str]/[:num]' . $name . ' - ' . $id . ' - ' . $satu . '-' . $dua;
});
$r->run(); // run to route

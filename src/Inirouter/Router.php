<?php
namespace Inirouter;

use Inirouter\Exception\BadRouteException;
use Inirouter\Exception\RouteNotFoundException;

class Router
{
    private $route; // instance of Route class

    /**
     * Router class constructor
     * used to save instance of a class
     * 
     * @param (object) Route $route class instance
     */
    public function __construct(Route $route)
    {
        $this->route = (object) $route;
    }

    /**
     * This function is used to call 'setRoute' function from Route class via
     * local variable $route which contain the Route class instance.
     * 
     * @param  array   $method  
     * @param  string  $path    
     * @param  mixed   $callback
     * @param  boolean $querystr
     * @return void
     */
    public function route($method, $path, $callback, $querystr = false)
    {
        try {
            $this->route->setRoute($method, $path, $callback, $querystr);
        } catch (BadRouteException $e) {
            print "[BadRouteException: " . $e->getMessage() . "]";
            print " in " . $e->getFile();
            print " line : " . $e->getLine();
            print "<br/>" . "\n";
            exit();
        }
    }

    public function run()
    {
        try {
            return $this->route->run();
        } catch (RouteNotFoundException $e) {
            print "[RouteNotFoundException: " . $e->getMessage() . "]";
            print " in " . $e->getFile();
            print " line : " . $e->getLine();
            print "<br/>" . "\n";
            exit();
        }
    }
}
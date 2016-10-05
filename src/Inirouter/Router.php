<?php
namespace Inirouter;

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
        $this->route->setRoute($method, $path, $callback, $querystr);
    }

    public function run()
    {
        return $this->route->run();
    }
}
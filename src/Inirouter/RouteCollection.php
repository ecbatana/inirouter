<?php
namespace Inirouter;

class RouteCollection
{
    /**
     * Variable that hold all passed route
     * 
     * @var array
     */
    private $collection = [];

    /**
     * function which used to save route into collection
     * 
     * @param array $route
     */
    public function setCollection($route)
    {
        $this->collection[] = $route;
    }

    /**
     * function that used to returning all routes
     * 
     * @return array       
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
<?php
namespace Inirouter;

class RouteConfig
{
    private $config; // contain route configuration

    /**
     * Route Config constructor
     */
    public function __construct()
    {
        $this->config = [
            'RouteServerRequest' => $_SERVER['REQUEST_URI'],
            'RouteRequestMethod' => $_SERVER['REQUEST_METHOD'],
            'RouteRequest' => explode('?', $_SERVER['REQUEST_URI'])[0],
            'RouteQueryString' => empty($_SERVER['QUERY_STRING']) ? '' : $_SERVER['QUERY_STRING'],
            'RouteRequestMethod' => $_SERVER['REQUEST_METHOD'],
            'RouteAllowedMethods' => ['GET', 'POST', 'PUT', 'DELETE', 'HEAD'],
        ];
    }

    /**
     * Getter function to get config variable
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
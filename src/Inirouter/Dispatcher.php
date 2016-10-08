<?php
namespace Inirouter;

use Inirouter\Exception\BadRouteException;

class Dispatcher
{
    private $dispatch; // contains the Dispatch class instance
    private $config; // contains the RouteConfig instance

    /**
     * initiator that get dependency from RouteClass that used to supporting
     * the dispatcher process.
     * 
     * @param  array  $config
     * @return void
     */
    public function init(array $config)
    {
        $this->config = $config;
    }

    /**
     * The main method of this Dispatcher class.
     *
     * @param  array $matchedRoute
     */
    public function dispatch($matchedRoute)
    {
        $callback = $matchedRoute['callback'];
        $methods = $matchedRoute['methods'];
        $requestMethod = $this->config['RouteRequestMethod'];
        $allowedMethods = $this->config['RouteAllowedMethods'];
        $request = $this->config['RouteServerRequest'];
        $queryString = $this->config['RouteQueryString'];
        $queryStringStatus = $this->queryStringStatus($matchedRoute['querystr']['status']);

        // check if method is allowed
        $this->methodCheck($methods, $requestMethod);

        // explode request with delimiter '/'
        $request = explode('/', $request);
        $explodedRequest = []; // set the exploded request container

        // begin iterate to check the request is root or not
        foreach ($request as $key => $value) {
            $explodedRequest[] = empty($value) ? $value . '/' : $value;
        }

        // explode the uri pattern of the matched root with delimiter
        // '-' and set into $pattern variable.
        $pattern = explode('-', $matchedRoute['path']['uri_pattern']);
        $param = []; // set the parameter container

        foreach ($pattern as $pK => $pV) {
            foreach ($explodedRequest as $xK => $xV) {
                foreach ($matchedRoute['path']['paramRegex'] as $rgx) {
                    if (preg_match($rgx, $pV) && $pK == $xK) {
                        $param[] = $xV;
                        break;
                    }
                }
            }
        }

        // Determine if query string status in the matched route is enabled
        // or not
        if ($queryStringStatus)
        {
            if (! empty($param)) {
                call_user_func_array($callback, $param);
            } else {
                call_user_func($callback);
            }
        } else {
            if ($queryStringStatus == false && empty($queryString)) {
                if (! empty($param)) {
                    call_user_func_array($callback, $param);
                } else {
                    call_user_func($callback);
                }
            } else {
                throw new BadRouteException("Query string is not allowed", 1);
                exit();
            }
        }
    }

    /**
     * Determine which the matched route query string status is enabled or not
     * return bool.
     * 
     * @param   $querystr
     * @return
     */
    private function queryStringStatus($querystr)
    {
        return $querystr == true ? true : false;
    }

    /**
     * Determine if method is allowed in action
     * 
     * @param  array $methods
     * @param  string $requestMethod
     */
    private function methodCheck($methods, $requestMethod)
    {
        $passedMethod = [];

        foreach ($methods as $method) {
            if ($method == $requestMethod) {
                $passedMethod[] = $method;
            }
        }

        if (empty($passedMethod)) {
            throw new BadRouteException("Your requested method is not allowed", 1);
            exit();
        }
    }
}
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
        $allowedMethods = $this->config['RouteAllowedMethods'];
        $request = $this->config['RouteServerRequest'];
        $queryString = $this->config['RouteQueryString'];
        $queryStringStatus = $matchedRoute['querystr']['status'];

        // check if method is allowed
        $this->methodCheck($methods, $allowedMethods);

        // begin iterate to check the request is root or not
        $request = $this->isRoot(explode('/', $request));

        // explode the uri pattern of the matched root with delimiter
        // '-' and set into $pattern variable.
        $pattern = explode('-', $matchedRoute['path']['uri_pattern']);


        // if matched route has an parameter, set it
        $param = $this->getRouteParam($pattern, $request, $matchedRoute['path']['paramRegex']);

        // begin to call the callback
        $this->call($queryString, $queryStringStatus, $callback, $param);
    }

    /**
     * Determine if request is root or not.
     */
    public function isRoot($request) {
        foreach ($request as $key => $value) {
            $request[] = empty($value) ? $value . '/' : $value;
        }

        return $request;
    }

    /**
     * Determine if matched route has an parameter or not.
     */
    public function getRouteParam($pattern, $request, $paramRegex) {
        $param = [];
        
        foreach ($pattern as $pK => $pV) {
            foreach ($request as $xK => $xV) {
                foreach ($paramRegex as $rgx) {
                    if (preg_match($rgx, $pV) && $pK == $xK) {
                        $param[] = $xV;
                        break;
                    }
                }
            }
        }

        return $param;
    }

    /**
     * To call the callback
     */
    public function call($queryString, $queryStringStatus, $callback, $param) {
        if ($queryStringStatus)
        {
            if (! empty($param)) {
                call_user_func_array($callback, $param);
            } else {
                call_user_func($callback);
            }
        } else {
            if (empty($queryString)) {
                if (! empty($param)) {
                    call_user_func_array($callback, $param);
                } else {
                    call_user_func($callback);
                }
            } else {
                throw new BadRouteException("Query string is not allowed", 1);
            }
        }
    }

    /**
     * Determine if method is allowed in action
     * 
     * @param  array $methods
     * @param  string $allowedMethod
     */
    private function methodCheck($methods, $allowedMethod)
    {
        $passedMethod = [];

        foreach ($methods as $method) {
            foreach ($allowedMethod as $allowed) {
                if ($method == $allowed) {
                    $passedMethod[] = $method;
                }
            }
        }

        if (empty($passedMethod)) {
            throw new BadRouteException("Your requested method is not allowed", 1);
        }
    }
}
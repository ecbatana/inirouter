<?php
namespace Inirouter;

use Inirouter\Exception\BadRouteException;

class RouteParser
{
    private $config; // hold the routeconfig that passed from initiator
    private $parsedRoute; // hold the parsed route

    /**
     * initiator that get dependency from RouteClass that used to supporting
     * the parser process.
     * 
     * @param  array  $config
     * @return void
     */
    public function init(array $config)
    {
        $this->config = $config;
    }

    /**
     * This is a main function of this class
     * parse class is used to run the parsed route, if parser has success and
     * returned boolean [true], then set them into local variable named
     * 'parsedRoute' via setParsed function and returning them.
     * 
     * @param  array $route
     * @return array on success
     */
    public function parse($route) {
    
        if ($this->runParser($route) === true)
        {
            $this->setParsed('methods', $this->parseMethod($route['methods']));
            $this->setParsed('path', $this->parsePath($route['path']));
            $this->setParsed('callback', $this->parseCallback($route['callback']));
            $this->setParsed('querystr', array(
                'status' => $route['querystr'],
            ));

            return $this->parsedRoute;
        }
    }

    /**
     * this function is used to run some parser, and check every parser.
     * if every parser has success. this function will return bool [true].
     * 
     * @param  array $route
     * @return [bool] true
     */
    public function runParser($route)
    {
        if ( is_array($this->parseMethod($route['methods'])) &&
             is_array($this->parsePath($route['path'])) &&
             $this->parseCallback($route['callback']) !== false
        ) {
            return true;
        }
    }

    /**
     * This is an method parser
     * process of this parser is, iterate the methods and allowed method and
     * check if method is available in allowed method. if available, pass
     * method into $parsedMethod variable.
     * in the end, return the result of validate if value of $parsedMethod is
     * not empty by checking via count() function.
     * 
     * @param  array $methods
     * @return array on success
     */
    public function parseMethod($methods)
    {
        if (is_array($methods)) {

            $parsedMethod = [];

            foreach ($methods as $method) {
                foreach ($this->config['RouteAllowedMethods'] as $allowedMethod) {
                    if ($method == $allowedMethod) {
                        $parsedMethod[] = $method;
                    }
                }
            }

            return count($parsedMethod) > 0 ? $parsedMethod : false;
        } else {
            throw new BadRouteException('[' . $methods . "] route method must be an array", 1);
        }
    }

    /**
     * This is an path parser
     * the main goal of this parser is parse the path into the specific path,
     * the specific category, which can contain the pattern of path, parameter 
     * / argument, etc.
     * 
     * @param  array $path
     * @return array
     */
    public function parsePath($path) {
        // explode first path with delimiter '/';
        $uri = explode('/', $path);

        // set up the structure of path which can contain parameter, uri
        // pattern, parameter pattern, and parameter regex for checking soon.
        $path = [
            'param' => [], // contain parameter of the path
            'paramPattern' => [], // contain full uri and parameter path
            'paramRegex' => '', // contain the parameter regex
            'uri_pattern' => '', // contain the patter of uri
        ];

        // Iterate exploded path in $uri variable
        // used to setup the path and insert it into $path variable
        foreach ($uri as $key => $value) {
            // Determine if exploded uri is an parameter or not
            if (preg_match('/([)?(?:[0-9A-Za-z])\w+?(?:])/', $value)) {
                $path['param'][] = $value . '/';
                $path['paramPattern'][] = $value;
                $path['uri_pattern'] .= $value . '/-';
            } else {
                $path['paramPattern'][] = $value;
                if (! empty($value)) {
                    $path['uri_pattern'] .= $value . '/-';
                } else {
                    $path['uri_pattern'] = '/-';
                }
            }
        }
        // set the root of pattern.
        $path['paramPattern'][0] = '/';
        $path['paramRegex'] = $this->paramRegexCreator($path['param']);

        // determine if uri_pattern is not root.
        if ($path['uri_pattern'] != '/-') {
            $path['uri_pattern'] = substr_replace($path['uri_pattern'], '', -1, 1);
        }

        // return path
        return $path;
    }

    /**
     * this function is used to convert parameter into regular expressions.
     * 
     * @param  array $param
     * @return array       
     */
    private function paramRegexCreator($param)
    {
        // Hold the matched parameter
        $matchedParam = [];

        // determine if parameter is empty array
        if (! empty($param) || $param !== [])
        {
            // if matched .. iterate to check if parameter is allowed and
            // generate the regular expression.
            foreach ($param as $key => $value) {
                switch ($value) {
                    case '[:num]/':
                        $matchedParam[] = '/^(\[)?(:+\w+)?(\])(\/)/';
                        break;

                    case '[:str]/':
                        $matchedParam[] = '/^(\[)?(:+\w+)?(\])(\/)/';
                        break;

                    case '[:all]/':
                        $matchedParam[] = '/^(\[)?(:+\w+)?(\])(\/)/';
                        break;
                    
                    default:
                        $value = substr_replace($value, '', -1, 1);
                        throw new BadRouteException($value . " parameter is not allowed", 1);
                        break;
                }
            }
        }

        // return
        return $matchedParam;
    }

    /**
     * This is callback parser
     * the process if this parser is first is determine if the callback is
     * callable or not.
     * 
     * @param  mixed $callback
     * @return 
     */
    public function parseCallback($callback)
    {
        if (is_callable($callback) == true)
        {
            return $callback;
        } else {
            // stay tune
        }
    }

    /**
     * this function is used to save parsed route into local variable named
     * $parsedRoute.
     * 
     * @param string $column
     * @param mixed  $value
     */
    public function setParsed($column, $value)
    {
        $this->parsedRoute[$column] = $value;
    }
}
<?php
namespace Inirouter;

use Inirouter\Exception\RouteNotFoundException;

class Route {
    private $config; // instance of RouteConfig class
    private $collection; // instance of RouteCollection class
    private $parser; // instance of RouteParser class
    private $dispatcher; // instance of Dispatcher class

    /**
     * Setting up construct to save some instance and run the initiator
     * 
     * @param   RouteConfig     $config         class instance
     * @param   RouteCollection $collection     class instance
     * @param   RouteParser     $parser         class instance
     * @param   Dispatcher      $dispatcher     class instance
     * @return  void
     */
    public function __construct(
        RouteConfig $config,
        RouteCollection $collection,
        RouteParser $parser,
        Dispatcher $dispatcher
    ) {
        // Save Instance
        $this->config = $config;
        $this->collection = $collection;
        $this->parser = $parser;
        $this->dispatcher = $dispatcher;

        // Run the initiator
        $this->init();
    }

    /**
     * This function is used to give dependency into required class.
     * @return void
     */
    public function init()
    {
        $this->parser->init($this->config->getConfig());
        $this->dispatcher->init($this->config->getConfig());
    }

    /**
     * Saving Route
     * This function is used to save route into collection, if the route
     * has passed from parser
     * 
     * @param   array     $methods 
     * @param   string    $path    
     * @param   mixed     $callback
     * @param   bool      $querystr
     */
    public function setRoute($methods, $path, $callback, $querystr)
    {
        // Wrap into one array
        $route = [
            'methods' => $methods,
            'path' => $path,
            'callback' => $callback,
            'querystr' => $querystr,
        ];
        
        // Begin validating, if route has passed and returning an array, 
        // save them into collection that located in RouteCollection class via
        // collection variable that hold the RouteCollection instance
        if(is_array($this->parse($route))) {
            $this->collection->setCollection($this->parse($route));
        }
    }

    /**
     * function which used to return saved route in collection
     * 
     * @return array
     */
    public function getCollection()
    {
        return $this->collection->getCollection();
    }

    /**
     * Run parse function from parser class
     * 
     * @param  array $route
     * @return array on success, and false on failure
     */
    private function parse($route) {
        return $this->parser->parse($route);
    }

    public function run()
    {
        // get and set config and route collection into variables
        $serverRequest = $this->config->getConfig()['RouteServerRequest'];
        $requestMethod = $this->config->getConfig()['RouteRequestMethod'];
        $request = $this->config->getConfig()['RouteRequest'];
        $queryString = $this->config->getConfig()['RouteQueryString'];
        $collection = $this->collection->getCollection();

        // set $matchedRoute variable to hold the matched route
        $matchedRoute = [];

        // Begin iterating collection
        foreach ($collection as $key => $value) {
            // snapshot the value into route
            $route = $value;

            // get the path / url pattern into $path variable
            $path = $value['path']['uri_pattern'];

            // determine if uri pattern / path is root.
            if ($path == '/-')
            {
                $path = '/'; // if is root, remove the strip with root only
                // determine is path as same as request, return bool
                $matches = ($path == $request) ? true : false;

                // if match, set matched route into variable $matchedRoute
                if ($matches == true)
                {
                    $matchedRoute = $route;
                }
            } else {
                // if its not root, then ..
                $path = explode('-', $path); // explode first /w delimiter '-'
                $pathRegex = '/^'; // set the first line of path regex
                $counter = 1; // counter which used later. start at 1

                // begin iterate $path
                foreach ($path as $key => $value) {
                    // match the value with some cases
                    switch ($value) {
                        case '[:num]/': // number cases
                            // determine if the loop is reach the last loop.
                            if ($counter == count($path)) {
                                $pathRegex .= '\d+?';
                            } else {
                                $pathRegex .= '\d+\/+';
                            }
                            break;

                        case '[:str]/': // string cases
                            // determine if the loop is reach the last loop.
                            if ($counter == count($path)) {
                                $pathRegex .= '\w+?';
                            } else {
                                $pathRegex .= '\w+\/+';
                            }
                            break;
                        
                        default: // default case / not an parameter
                            if ($value == '/') // if value is root
                            {
                                $value = substr_replace($value, '', -1, 1);
                                $pathRegex .= $value . '\/+';
                            } elseif ($counter == count($path)) { // last loop
                                $value = substr_replace($value, '', -1, 1);
                                $pathRegex .= $value . '+?';
                            } else { // if value is not root
                                $value = substr_replace($value, '', -1, 1);
                                $pathRegex .= $value . '\/+';
                            }
                            break; // break to prevent duplication
                    }

                    // auto increment counter until the loop end.
                    $counter++;
                }

                // remove the last one character
                $pathRegex = substr_replace($pathRegex, '', -1, 1);
                $pathRegex .= '$/'; // set the end line of regex

                // begin matching if request is matched with the regular 
                // expression, if matched, set the matched route ($route) 
                // into the variable named '$matchedRoute'.
                $matches = preg_match($pathRegex, $request);
                if ($matches == true && $matchedRoute == [] && empty($matchedRoute))
                {
                    $matchedRoute = $route;
                }
            }
        }

        // Before send to dispatcher,
        // Determine if matchedRoute is not empty
        if (! empty($matchedRoute))
        {
            $this->dispatcher->dispatch($matchedRoute);
        } else {
            throw new RouteNotFoundException("Your Request is Not Found", 1);
        }

        // cleanup
        $matchedRoute = '';
    }
}
<?php
namespace Inirouter;

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
        // Get the server request uri
        $request = $this->config['RouteServerRequest'];
        $queryString = $this->config['RouteQueryString'];

        // Determine if query string status in the matched route is enabled
        // or not
        if ($this->queryStringStatus($matchedRoute['querystr']['status']))
        {
            // explode request with delimiter '/'
            $request = explode('/', $request);
            $explodedRequest = []; // set the exploded request container

            // begin iterate to check the request is root or not
            foreach ($request as $key => $value) {
                empty($value) ? $explodedRequest[] = $value . '/' : $explodedRequest[] = $value;
            }

            // explode the uri pattern of the matched root with delimiter
            // '-' and set into $pattern variable.
            $pattern = explode('-', $matchedRoute['path']['uri_pattern']);
            $param = []; // set the parameter container

            // begin looping ..
            // first loop is iterate exploded uri pattern of the matched
            // route in variable $pattern. second loop is iterate exploded
            // server request that called $explodedRequest variable. and 
            // the third loop or the last loop is iterate the param regex
            // that detect how amount parameter (especially 
            // parameter regular expression) are declared in the 
            // matched route.
            // 
            // $pk stands for 'patternKey', refer to $pattern variable
            // $pV stands for 'patternValue', refer to $pattern variable
            // 
            // $xk stands for 'explodedRequestKey', refer to 
            // $explodedRequest variable
            // $xV stands for 'explodedRequestValue', refer to 
            // $explodedRequest variable
            // 
            // $rgx stands for 'regex', refer to matched route parameter
            // regex
            foreach ($pattern as $pK => $pV) {
                foreach ($explodedRequest as $xK => $xV) {
                    foreach ($matchedRoute['path']['paramRegex'] as $rgx) {
                        // Begin to check if pattern ($pV) is matched from
                        // matched route parameter regex.
                        // and check too if pattern key as match as 
                        // exploded request.
                        // 
                        // if match then get the matched parameter and
                        // set into parameter container called '$param'
                        // variable.
                        if (preg_match($rgx, $pV) && $pK == $xK) {
                            $param[] = $xV;
                            break;
                        }
                    }
                }
            }

            // Begin calling with determine if the matched route own 
            // parameter or not.
            if (! empty($param)) {
                call_user_func_array($matchedRoute['callback'], $param);
            } else {
                call_user_func($matchedRoute['callback']);
            }
        } else { // if query string status not enabled then
            // determine if query string is not called
            if (empty($queryString)) {
                    
                // explode request with delimiter '/'
                $request = explode('/', $request);
                $explodedRequest = []; // set the exploded request container

                // begin iterate to check the request is root or not
                foreach ($request as $key => $value) {
                    empty($value) ? $explodedRequest[] = $value . '/' : $explodedRequest[] = $value;
                }

                // explode the uri pattern of the matched root with 
                // delimiter '-' and set into $pattern variable.
                $pattern = explode('-', $matchedRoute['path']['uri_pattern']);
                $param = []; // set the parameter container

                // begin looping ..
                // first loop is iterate exploded uri pattern of 
                // the matched route in variable $pattern. second loop is
                // iterate exploded server request that called 
                // $explodedRequest variable. and the third loop or the 
                // last loop is iterate the param regex that detect how 
                // amount parameter (especially parameter regular 
                // expression) are declared in the matched route.
                // 
                // $pk stands for 'patternKey', refer to $pattern variable
                // $pV stands for 'patternValue', refer to 
                // $pattern variable
                // 
                // $xk stands for 'explodedRequestKey', refer to 
                // $explodedRequest variable
                // $xV stands for 'explodedRequestValue', refer to 
                // $explodedRequest variable
                // 
                // $rgx stands for 'regex', refer to matched 
                // route parameter regex
                foreach ($pattern as $pK => $pV) {
                    foreach ($explodedRequest as $xK => $xV) {
                        foreach ($matchedRoute['path']['paramRegex'] as $rgx) {
                            // Begin to check if pattern ($pV) is 
                            // match with matched route parameter regex.
                            // and check too if pattern key as match as 
                            // exploded request.
                            // 
                            // if match then get the matched parameter and
                            // set into parameter container called '$param'
                            // variable.
                            if (preg_match($rgx, $pV) && $pK == $xK) {
                                $param[] = $xV;
                                break;
                            }
                        }
                    }
                }

                // Begin calling with determine if the matched route own 
                // parameter or not.
                if (! empty($param)) {
                    call_user_func_array($matchedRoute['callback'], $param);
                } else {
                    call_user_func($matchedRoute['callback']);
                }
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
}
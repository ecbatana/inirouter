# inirouter
an another fast php router


>   Basic Usage

    // load the autoloader from src directory    
    require('src/Autoload.php');

    // Initialize autoloader
    $autoload = new Autoload;
    $autoload->run();

    // begin to save route
    // in example, we set the '/' request, with callback function that
    // return an string 'Coming from /'
    $r->route(['GET'], '/', function() {
        echo 'Coming from /';
    });

    // begin to process the route
    // Note that run() function must be called in the end of code.
    $r->run()

>   route() function argument

    $r->route($method, $path, $callback, $querystr = false);

>   the first is `$method` argument which used to hold the allowed method for 
>   route, must be an array.
>   allowed method are including 'GET', 'POST', 'PUT', 'DELETE', 'HEAD'.
>   the second is `$path` argument which used to hold the route path / uri.
>   the third is `$callback` argument which used to hold the callback for the 
>   route, for now will only support function / closure as callback. stay tune.
>   and the last is `$querystr` argument which used to hold that is route will
>   allow the query string or not. if route want to allow querystring, then set 
>   into `true`, but if don't, just left it empty (`false` on default ) or set 
>   into `false`.
<?php

class Route
{
    /**
     * @var [type]
     */
    public $routes = [];

    /**
     * @var array
     */
    private $validRegex = [
      ":alphanum" => ALPHA_NUM,
      ":alpha" => ALPHA,
      ":digits" => DIGITS,
      ":page" => PAGE
    ];

    /**
     * @var [array]
     */
    private $middleware = [];

    /**
     * @var [string]
     */
    private $currentUrl;

    /**
     * @var [bool]
     */
    private $isRegexRouteAdded = false;

    /**
     * @var [array]
     */
    private $matchedRegex = [];

    /**
     * @var [array]
     */
    private $middlewareStack = [];


    /**
     * Add a new route to the router.
     * @param [string] $url
     * @param [string] $method - server method
     * @param [string] $dst - controller action
     */
    public function add($url, $method, $dst)
    {
        if (!isset($url) || !is_string($url)) {
            throw new Exception("Bad url format. Url must be a string.");
        }
        if (!is_callable($dst)) {
            $info = explode('@', $dst);
        }
        if (isset($info[1]) && isset($info[0])) {
            $call = $info[1];
            $class = $info[0];
        } elseif (is_callable($dst)) {
            $call = $dst;
        } else {
            throw new Exception("No class or function call in route");
        }
        $this->addRequest($url, $method, $class ?? '', $call);
        return $this;
    }

    /**
     * Check if the current stored route has a regex.
     * @param  [string] $url - stored route url
     * @return [array] or null if this route doesn't have any regex.
     */
    private function getRegexUrlPos($url)
    {
        $regex = [];
        $splitUrl = explode('/', $url);
        foreach ($splitUrl as $key => $value) {
            if (isset($this->validRegex[$value])) {
                $regex[] = $key;
            }
        }
        return (!empty($regex) ? $regex : null);
    }

    /**
     * Add routes and give priority to none regex's routes.
     * @param [string] $url - route url
     * @param [string] $method - method controller
     * @param [string] $class - controller
     * @param [string] $call - function, if no controller is provided
     */
    private function addRequest($url, $method, $class, $call)
    {
        $regexPos = $this->getRegexUrlPos($url);
        $routeSettings = [
          'method' => $method,
          'url' => $url,
          'class' => $class,
          'method_call' => $call,
          'regex_pos' => $regexPos
        ];
        if ($regexPos === null) {
            $this->isRegexRouteAdded = false;
            array_unshift($this->routes, $routeSettings);
        } else {
            $this->isRegexRouteAdded = true;
            $this->routes[] = $routeSettings;
        }
    }

    /**
     * Match the corresponding regex route with the current server url.
     * @param  [string] $url - stored route url
     * @param  [string] $currentUrl - server request uri
     * @param  [int] $regexPos - regex position in the stored route url
     * @return [bool]
     */
    public function getRegex($url, $currentUrl, $regexPos)
    {
        if ($regexPos === null) {
            return (false);
        }
        $split = explode('/', $url);
        $currUrlSplit = explode('/', $currentUrl);
        $validRegex = [];
        foreach ($regexPos as $value) {
            if (isset($currUrlSplit[$value], $split[$value], $this->validRegex[$split[$value]])) {
                if (preg_match($this->validRegex[$split[$value]], $currUrlSplit[$value])) {
                    $validRegex[$value] = $currUrlSplit[$value];
                }
            }
        }
        $cmp = array_replace($split, $validRegex);
        if (empty($validRegex)) {
            return (false);
        }
        $this->matchedRegex = array_values($validRegex);
        $cmp = array_replace($split, $validRegex);
        return ($cmp === $currUrlSplit);
    }

    /**
     * Load the corresponding controller's class and method and add the matched regex values.
     * @param  [string] $name
     * @param  [string] $method
     * @param  [mixed]  $param
     * @return [bool]
     */
    private function getClass($name, $method, $param = null)
    {
        if (!isset($name, $method)) {
            return ;
        }
        $this->runMiddleware();
        $path = dirname(__DIR__)."/controller/".$name.".php";
        if (file_exists($path)) {
            require_once($path);
        }
        if (class_exists($name) && method_exists($name, $method)) {
            $init = new $name();
            if (isset($param)) {
                $init->regex = $this->matchedRegex;
                $init->{$method}($param);
            } else {
                $init->{$method}();
            }
            return (true);
        }
        return (false);
    }

    /**
     * Match an existing route and load the corresponding controller's action.
     * @return [bool] or 404 page not found if this route doesn't exist.
     */
    public function loadRoutes()
    {
        $store_id = [];
        $currentUrl = $_SERVER['REQUEST_URI'];
        $serverMethod = $_SERVER['REQUEST_METHOD'];
        foreach ($this->routes as $route) {
            $method = $route['method'];
            $url = $route['url'];
            $class = $route['class'];
            $classMethod = $route['method_call'];
            $regexPos = $route['regex_pos'];

            $this->currentUrl = $url;
            if ($class == '' && is_callable($classMethod)
              && $method == strtolower($serverMethod)
              && $currentUrl == $url) {
                $this->runMiddleware();
                return ($classMethod());
            }

            if ($this->getRegex($url, $currentUrl, $regexPos) && $method == strtolower($serverMethod)) {
                return ($this->getClass(
                    $class,
                    $classMethod,
                    array_values(array_slice(explode('/', $currentUrl), -1))[0]
                ));
            }
            if (isset($_GET) && $method == strtolower($serverMethod)) {
                if ($currentUrl == $url) {
                    return ($this->getClass($class, $classMethod));
                }
            }
            if (isset($_POST) && $method == strtolower($serverMethod)) {
                if ($currentUrl == $url) {
                    return ($this->getClass($class, $classMethod));
                }
            }
        }
        $pageNotFoundView = dirname(__DIR__).'/views/page_404.php';
        if (file_exists($pageNotFoundView)) {
            require_once($pageNotFoundView);
        }
    }

    /**
     * Add a new middelware function for the current route.
     * @param [function] $target
     */
    public function addMiddleware($target)
    {
        if ($this->isRegexRouteAdded === false) {
            $lastRoute = $this->routes[0]['url'];
        } else {
            $lastRoute = $this->routes[key(array_slice($this->routes, -1, 1, true))]['url'];
        }
        if (is_callable($target)) {
            $this->middleware[$lastRoute] = $target;
        }
    }

    /**
     * Add a new collection of routes attached to a middelware function.
     * @param [array] $target
     */
    public function addMiddlewareStack($target)
    {
        if (is_array($target) && isset($target['callback'])
          && is_callable($target['callback']) && count($target) > 1) {
            $this->middlewareStack[] = $target;
        }
    }

    /**
     * Load a middelware function from a collection of routes or from one route.
     * @return [void]
     */
    public function runMiddleware()
    {
        if (isset($this->middleware[$this->currentUrl])
        && is_callable($this->middleware[$this->currentUrl])) {
            $this->middleware[$this->currentUrl]();
        }
        if (isset($this->middlewareStack) && is_array($this->middlewareStack)) {
            foreach ($this->middlewareStack as $middlewareGroup) {
                if (in_array($this->currentUrl, $middlewareGroup) && is_callable($middlewareGroup['callback'])) {
                    return ($middlewareGroup['callback']());
                }
            }
        }
    }
}

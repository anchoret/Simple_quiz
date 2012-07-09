<?php
namespace Kernel\Routing;

use Kernel\HTTP\Request;
use Kernel\Routing\Loaders;
use Kernel\Routing\RouteNode;
use Kernel\Exceptions;
/**
 * Simple router service. Implements singleton.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class Router
{
    const CACHE_FILE = '/var/www/quiz/cache/routing.cache';
    /**
     * Label for optional path's variable
     */
    const OPT = '_opt_';
    /**
     * Label for required path's variable
     */
    const REQ = '_req_';

    /**
     * @var \Kernel\Routing\Loaders\Router
     */
    private static $instance = null;

    /**
     * @var \Kernel\Routing\Loaders\RouterLoadeInterface
     */
    private $loader = null;

    /**
     * @var array
     */
    private $routing = array();

    /**
     * @var string
     */
    private $resource;

    /**
     * @var \Kernel\HTTP\Request
     */
    private $request;

    /**
     *
     * @param \Kernel\HTTP\Request $request
     * @param string $resource
     * @return \Kernel\Routing\Router
     */
    public static function getInstance($request = null, $resource = null)
    {
        if (self::$instance === null){
            self::$instance = new static ($request, $resource);
        }

        return self::$instance;
    }
    /**
     * Find needle router by request object
     *
     * @return \Kernel\Routing\Router
     */
    public function findRoute()
    {
        $current = $this->getRouteTree();
        $paths = explode('/', $this->request->getRequestURI());
        if (count($paths) == 1 && $paths[0] == '') {
            $route = $this->_findEmptyRoute($current);
        } else {
            $route = $this->_findRoute($current, $paths);
        }
        if ($route instanceof Route) {

            return $route;
        } else {
            throw new Exceptions\RouteNotFoundException(
                    $this->request->getRequestURI()?:'/');
        }
    }

        /**
     * Generate URL by parameters.
     *
     * @param string $name
     * @param array $options
     * @param boolean $absolute
     */
    public function generateURl($name, array $options = array(),
        $absolute = false, $https = false)
    {
        $routeTable = $this->getRouteTable();
        if (isset($routeTable[$name])) {
            $route = $routeTable[$name];
        } else {
            throw new Exceptions\UndefinedRouteException($name);
        }
        $defaults = $route->getDefaults();
        $patArray = array();
        $repArray = array();
        foreach ($route->getOptions() as $optName) {
            if (!isset($options[$optName]) &&
                !isset($defaults[$optName])){

                throw new Exceptions\WrongParameterRouteException($optName, $name);
            }
            $patArray [] = '/{' . $optName . '}/';
            $repArray [] =
                isset($options[$optName])?$options[$optName]:'';
        }
        $patArray [] = '/\/{2,}/';
        $repArray [] = '/';
        $url = preg_replace($patArray, $repArray, $route->getPattern());
        if ($absolute) {
            $url = ($https ? 'https' : 'http') . '://' .
                $this->request->getServerVariable('SERVER_NAME') . '/' . $url;
        }

        return urlencode($url);
    }

    public function getParameters(Route $route)
    {
        $paths = explode('/', $this->request->getRequestURI());
        $parts = explode('/', $route->getPattern());
        $count = 0;
        $parameters = array();
        foreach ($parts as $part) {
            if ($part != '' && preg_match('/^{[a-zA-Z]+}$/', $part)) {
                if (isset($paths[$count])) {
                    $parameters[substr($part, 1, strlen($part)-2)] = $paths[$count];
                }
            }
            if ($part != '') {
                ++$count;
            }
        }

        return $parameters;
    }

    protected function _findRoute (RouteNode $current, $paths)
    {
        for ($i = 0; $i < count($paths); $i++){
            if (($node = $current->getChild($paths[$i])) !== false) {
                $current = $node;
            } else {
                if (($node = $current->getChild(self::REQ)) !== false) {
                    $current = $node;
                    if( ($route = $this->_findRoute($node, array_slice($paths, 1)))
                        instanceof Route && $this->isSameRequestMethod($route)) {

                        return $route;
                    }
                }
                if  (($node = $current->getChild(self::OPT)) !== false) {
                    $current = $node;
                    if( ($route = $this->_findRoute($node, array_slice($paths, 1)))
                        instanceof Route && $this->isSameRequestMethod($route)) {

                        return $route;
                    }
                }
            }
        }
        if (($route = $current->getRoute()) instanceof Route &&
            $this->isSameRequestMethod($route)) {

            return $route;
        } else {

            return $this->_findEmptyRoute($current);
        }
    }

    protected function _findEmptyRoute (RouteNode $node)
    {
        do {
            if(($route = $node->getRoute()) instanceof Route &&
                $this->isSameRequestMethod($route)) {

                return $node->getRoute();
            }
        } while($node = $node->getChild(self::OPT));

        return false;
    }

    protected function isSameRequestMethod(Route $route)
    {
        if (Request::ALL_METHOD == $route->getType() ||
            $route->getType() == $this->request->getRequestMethod()) {

            return true;
        } else {
            return false;
        }
    }

    protected function __construct(Request $request, $resource)
    {
        $this->resource = $resource;
        $this->request = $request;
    }

    protected function getRouteTree()
    {
        if (!isset($this->routing['tree'])) {
            $this->loadRoutingMap();
        }
        return $this->routing['tree'];
    }

    protected function getRouteTable()
    {
        if (!isset($this->routing['table'])) {
            $this->loadRoutingMap();
        }
        return $this->routing['table'];
    }

    protected function loadRoutingMap()
    {
        if (!file_exists(self::CACHE_FILE) ||
            filemtime(self::CACHE_FILE) <= filemtime($this->resource)) {

            $this->buildRoutingMap();
            if ($file = fopen(self::CACHE_FILE, "w")) {
                fwrite($file, serialize($this->routing));
                fclose($file);
            }
        } else {
            $this->routing = unserialize(file_get_contents(self::CACHE_FILE));
        }
    }

    protected function buildRoutingMap()
    {
        $routesArray = $this->getLoader()->load();
        $this->routing = array(
            'tree'  => new RouteNode('/', null),
            'table' => array()
        );
        $this->routing['table'] = $routesArray;
        foreach ($routesArray as $route) {
            $segments = explode('/', $route->getPattern());
            array_shift($segments);
            $current = $this->routing['tree'];
            $previosOptional = false;
            foreach ($segments as $segment) {
                if (preg_match('/^{[a-zA-Z]+}$/', $segment)) {
                    $segment = substr($segment, 1, strlen($segment)-2);
                    if (key_exists($segment, $route->getDefaults())){
                        //path's segment is optional variable
                        $segment = self::OPT;
                    } else {
                        //path's segment is required variable
                        $segment = self::REQ;
                    }
                } elseif(preg_match('/^[^{][a-zA-Z]+[^}]$/', $segment)) {
                    //path's segment is static word
                } elseif ($segment == "" && count($segment) == 1) {
                    $current->setRoute($route);
                } else {
                    throw new Exceptions\WrongRoutePatternException($route,
                        'Некорректная маска маршрута.');
                }
                if ($previosOptional && $segment != self::OPT) {
                    throw new Exceptions\WrongRoutePatternException($route,
                        "Неправильная последовательность частей адреса. " .
                         "За необязательным параметром может следовать только необязательный");
                }
                if (($node = $current->getChild($segment)) === false){
                    $node = new RouteNode($segment, $current);
                    $current->addChild($node);
                }
                $current = $node;
                if ($segment == self::OPT ) {
                    $previosOptional = true;
                }
            }
        $current->setRoute($route);
        }
    }

    protected function getLoader()
    {
        if ($this->loader === null || ! $this->loader instanceof Loaders\RouterLoadeInterface) {
            $factory = new Loaders\RoutersLoaderFactory();
            $this->loader = $factory->getLoader($this->resource);
        }

        return $this->loader;
    }

    //Part of singleton's implementation.
    protected function __wakeup(){
    }
    protected function __clone(){
    }
}
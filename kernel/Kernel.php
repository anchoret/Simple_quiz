<?php
namespace Kernel;

use Kernel\Routing\Router;
use Kernel\HTTP\Request;
use Kernel\Controller\AbstractController;
use Kernel\ServiceContainer\Container;

class Kernel
{
    private $classLoader = null;

    private $request;

    private $route;

    private $controller;

    public function __construct($mode)
    {
        if ($mode == 'dev') {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            error_reporting(E_ALL);
        }
    }
    public function boot()
    {
        require_once 'ClassLoader.php';
        $loader = $this->getClassLoader();
        $loader->addNamespaces(array(
            'Kernel' => __DIR__
        ));
        $loader->setFailNamespace(__DIR__.'/../source');
        $loader->register();

        return $this;
    }

    public function start()
    {
        $this->request = new Request();
        $this->request->bindGlobalVars();

        $router = Router::getInstance($this->request, __DIR__.'/../config/routing.ini');
        $container = Container::getInstance();
        $container->add('kernel.router', $router);
        $this->route = $router->findRoute();

        $this->controller = $this->findController($this->route->getAction());

        $refMethod = new \ReflectionMethod($this->controller[0],
            $this->controller[1]);
        $paramNeedleArray = $refMethod->getParameters();
        $paramArray = $router->getParameters($this->route);
        $paramValuesArray = array_merge($this->route->getDefaults(), $paramArray);
        $callParams = array();
        foreach ($paramNeedleArray as $param) {
            if (!isset($paramValuesArray[$param->getName()])) {
                if ($param->isOptional()) {
                    $callParams[$param->getPosition()] = $param->getDefaultValue();
                } else {
                    throw new Exceptions\MissingRequiredParameterException(
                        $param->getName(), $this->controller[0], $this->controller[1]);
                }
            } else {
                $callParams[$param->getPosition()] = $paramValuesArray[$param->getName()];
            }
        }
        $controller = $this->controller[0];
        $controller->setContainer($container);
        ob_start();
        $response = call_user_func_array($this->controller, $callParams);

        if (!$response instanceof HTTP\Response) {
            throw new Exceptions\WrongActionReturnParameterException($response,
                get_class($this->controller[0]) . "::" . $this->controller[1]);
        } else {
            $response->sendToClient();
        }
    }

    public function getClassLoader()
    {
        if ($this->classLoader == null) {
            $this->classLoader = new ClassLoader();
        }

        return $this->classLoader;
    }

    protected function findController($pattern)
    {
        if (3 != count($parts = explode(':', $pattern))) {
            throw new Exceptions\InvalidActionPatternException($pattern);
        }

        list($module, $controller, $action) = $parts;
        $tryClass = $module. 'Module\\Controller\\'.$controller.'Controller';
        if (!class_exists($tryClass)) {
            throw new Exceptions\NotFoundControllerException($tryClass);
        } else {
            $controllerObject = new $tryClass;
            if (!$controllerObject instanceof AbstractController) {
                throw new Exceptions\NotFoundControllerException($tryClass);
            }
        }
        $tryCallable = array($controllerObject, $action.'Action');
        if (!is_callable($tryCallable)) {
            throw new Exceptions\NotFoundActionException($tryClass, $action.'Action');
        } else {
            return $tryCallable;
        }
    }
}

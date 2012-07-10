<?php
namespace Kernel\ServiceContainer;

use ProxyService;
use Kernel\Exceptions;

/**
 * Service and global parameters container.
 * Implementing Singleton
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class Container {

    const MAIN_CONFIG = 'parameters.ini';

    private $params = array();

    private $services = array();

    private static $instance = null;

    private function __construct()
    {
        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..';
        $this->setParameter('kernel.root_dir', $rootDir);
        $config = parse_ini_file($this->getParameter('kernel.root_dir') .
            DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . self::MAIN_CONFIG, true);
        $this->fillContainer($config);
    }

    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new static ();
        }

        return self::$instance;
    }

    public function get($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        } else {
            throw new Exceptions\UndefinedServiceContainerException($name);
        }
    }

    public function getParameter($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else {
            throw new Exceptions\UndefinedContainerParameterException($name);
        }
    }

    private function setParameter($name, $value)
    {
        if (preg_match_all('/%[a-z._]+%/', $value, $matches)){
            //die(var_dump($matches));
            $patArray = array();
            $repArray = array();
            foreach ($matches[0] as $match) {
                $patArray[] = '/' . $match . '/';
                $param = substr($match, 1, strlen($match)-2);
                $repArray[] = $this->getParameter($param);
            }
            $value = preg_replace($patArray, $repArray, $value);
        }
        $this->params[$name] = $value;
    }

    public function __get($name)
    {
        $this->getParameter($name);
    }

    private function fillContainer(array $config)
    {
        foreach ($config as $section => $values) {
            foreach($values as $name => $value) {
                $name = $section . '.' . $name;
                $this->setParameter($name, $value);
            }
        }
    }

    private function setService($name,array $values)
    {
        $this->services[$name] = $this->createProxyService($name, $values);
    }

    private function createProxyService($name,array $values)
    {

    }
}

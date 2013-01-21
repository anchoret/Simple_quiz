<?php
namespace Kernel\ServiceContainer;

use Kernel\ServiceContainer\ProxyService;
use Kernel\Exceptions;

/**
 * Service and global parameters container.
 * Implementing Singleton
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class Container {

    const MAIN_CONFIG = 'parameters.ini';

    const INDIVIDUAL_CONFIG_PREFIX = 'individual.';

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
        $individualConfigFile = $this->getParameter('kernel.root_dir') .
            DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . self::INDIVIDUAL_CONFIG_PREFIX . self::MAIN_CONFIG;
        if (is_readable($individualConfigFile)) {
            $individualConfig = parse_ini_file($individualConfigFile, true);
            $config = array_replace_recursive($config, $individualConfig);
        }
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
            if ($this->services[$name] instanceof ProxyService) {
                $this->services[$name] = $this->services[$name]->createService();
            }
            return $this->services[$name];
        } else {
            throw new Exceptions\UndefinedServiceContainerException($name);
        }
    }

    public function add($name, $object)
    {
        if (!isset($this->services[$name])) {
            $this->services[$name] = $object;

            return true;
        }

        return false;
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
        $services = parse_ini_file($this->getParameter('common.services'), true);
        foreach ($services as $name => $values) {
            $this->setService($this->createProxyService($name, $values));
        }
    }

    private function setService(ProxyService $proxy)
    {
        $this->services[$proxy->getName()] = $proxy;
    }

    private function createProxyService($serviceName, array $values)
    {
        $class = $values['class'];
        $params = array();
        foreach ($values as $name => $value) {
            if ($name == 'class') {
                continue;
            }
            if (preg_match('/^%[a-z.]+%$/', $value)) {
                $params[$name] = $this->getParameter(substr($value, 1, strlen($value)-2));
            } else {
                $params[$name] = $value;
            }
        }
        return new ProxyService($this, $serviceName, $class, $params);
    }
}

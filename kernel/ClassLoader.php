<?php
namespace Kernel;

class ClassLoader
{
    private $registerNamespaces = array();

    private $failNamespace = '';

    public function addNamespaces(array $namespaces)
    {
        $this->registerNamespaces = array_merge(
            $this->registerNamespaces,
            $namespaces
        );
    }

    public function setFailNamespace($namespace)
    {
        $this->failNamespace = (string) $namespace;
    }

    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    public function loadClass($class)
    {
        $startPosition = strpos($class, '\\');
        $prefix = substr($class, 0, $startPosition);
        if (isset($this->registerNamespaces[$prefix])) {
            $directory = $this->registerNamespaces[$prefix];
            $additional = '';
        } else {
            $directory = $this->failNamespace;
            $additional = $prefix . DIRECTORY_SEPARATOR;
        }
        $className = substr($class, $startPosition + 1);
        $file = $directory . DIRECTORY_SEPARATOR . $additional .
            str_replace('\\', DIRECTORY_SEPARATOR, $className) .'.php';
        if (file_exists($file)) {
            include $file;
        }
    }
}
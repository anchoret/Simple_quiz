<?php
namespace Kernel;

class Kernel
{
    protected $classLoader = null;

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
    }

    public function getClassLoader()
    {
        if ($this->classLoader == null) {
            $this->classLoader = new ClassLoader();
        }

        return $this->classLoader;
    }

}

<?php
namespace Kernel\ServiceContainer;

/**
 * Proxy service's class for service container lazy loading.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class ProxyService {

    private $name;

    private $class;

    private $params = array();

    public function __construct($name, $class, $params)
    {
        $this->name = $name;
        $this->class = $class;
        $this->params = $params;
    }

    public function createService()
    {
        try {
            $refClass = new \ReflectionClass($this->getClass());
            $constructor = $refClass->getConstructor();
            $paramNeedleArray = $constructor->getParameters();
            $callParams = array();
            foreach ($paramNeedleArray as $param) {
                if (!isset($this->params[$param->getName()])) {
                    if ($param->isOptional()) {
                        $callParams[$param->getPosition()] = $param->getDefaultValue();
                    } else {
                        throw new Exceptions\MissingRequiredParameterException(
                            $param->getName(), $this->controller[0], '__construct');
                    }
                } else {
                    $callParams[$param->getPosition()] = $this->params[$param->getName()];
                }
            }

            return $refClass->newInstanceArgs($callParams);
        } catch (\Exception $e){
            throw new Exceptions\ErrorServiceConstructException($name, $class, $params);
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getClass() {
        return $this->class;
    }

    public function getParams() {
        return $this->params;
    }
}

<?php
namespace Kernel\Entity;

use Kernel\ServiceContainer\Container;
/**
 * Parent class for all entities
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class AbstractEntity {

    protected $id = null;

    protected $hash = false;

    protected $class;

    protected $file;

    protected function load(array $properties = array(), $class, $file)
    {
        $this->class = $class;
        $this->file = $file;
        //$map = $this->_getMap();
        foreach ($properties as $property=>$value) {
            $method = 'set'.ucfirst(isset($map[$property])?$map[$property]:$property);
            $this->$method($value);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function toArray(){
        $map = $this->_getMap();
        $result = array();
        foreach ($map as $column => $property) {
            $method = 'get' . ucfirst($property);
            $result[$column] = $this->$method();
        }

        return $result;
    }

    protected function _getMap()
    {
        $container = Container::getInstance();
        $dbTableManager = $container->get('db.table.manager');
        return $dbTableManager->_getMap(
            $this->class,
            $this->file
        );

    }

    private function getHash() {
        return $this->hash;
    }
    /**
     *
     * @param type $args
     * @return \Kernel\Entity\AbstractEntity
     */
    private function setHash($args) {
        $hash = sha1(implode('-',$this->toArray()));
        $this->hash = $this->hash ?:$hash;

        return $this;
    }
}

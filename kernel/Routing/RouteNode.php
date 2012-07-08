<?php
namespace Kernel\Routing;
/**
 * Description of RouteNode
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class RouteNode {
    /**
     * @var \Kernel\Routing\RouteNode | null for root node
     */
    private $parent = null;

    /**
     * @var array of \Kernel\Routing\RouteNode
     */
    private $childs = array();

    /**
     * @var \Kernel\Routing\Route | null
     */
    private $route = null;

    /**
     * @var string
     */
    private $segment;

    public function __construct($segment, $parent, $route = null, array $childs = array())
    {
        if (!$parent instanceof RouteNode) {
            $parent = null;
        }
        $this->setParent($parent)
            ->setSegment($segment)
            ->setChilds($childs)
            ->setRoute($route);
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;

        return $this;
    }

    public function getChilds() {
        return $this->childs;
    }

    public function setChilds(array $childs) {
        $this->childs = $childs;

        return $this;
    }

    public function getRoute() {
        return $this->route;
    }

    public function setRoute($route) {
        if (null !== $route && !$route instanceof Route) {
            $route = null;
        }
        $this->route = $route;

        return $this;
    }

    public function addChild(RouteNode $node)
    {
        $this->childs[$node->getSegment()] = $node;

        return $this;
    }

    public function getChild($segment)
    {
        return isset($this->childs[$segment]) ? $this->childs[$segment] : false;
    }


    public function getSegment() {
        return $this->segment;
    }

    public function setSegment($segment) {
        $this->segment = $segment;

        return $this;
    }
}

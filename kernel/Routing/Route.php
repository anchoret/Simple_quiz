<?php
namespace Kernel\Routing;

/**
 * Route entity.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class Route
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $action;

    public function __construct($name, $pattern, $action, array $defaults = array(),array $options = array(), $type = 'GET')
    {
        $this->setName($name)
            ->setPattern($pattern)
            ->setDefaults($defaults)
            ->setOptions($options)
            ->setType($type)
            ->setAction($action);
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function setPattern($pattern) {
        $pat = array(
            '/\s\s+/',
            '/\/{2,}/'
        );
        $rep = array(
            '',
            '/'
        );
        $pattern = preg_replace($pat, $rep, $pattern);
        if ('/' != $pattern[0]) {
            $pattern = '/' . $pattern;
        }
        $length = strlen($pattern);
        if ($length > 1 && '/' == $pattern[$length-1]) {
            $pattern = substr($pattern, 0, $length-1);
        }
        $this->pattern = $pattern;

        return $this;
    }

    public function getDefaults() {
        return $this->defaults;
    }

    public function setDefaults($defaults) {
        $this->defaults = $defaults;

        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;

        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $type = strtoupper($type);
        if ('POST' == $type || 'ALL' == $type ) {
            $this->type = $type;
        } else {
            $this->type = 'GET';
        }

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;

        return $this;
    }
}
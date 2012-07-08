<?php
namespace Kernel\Exceptions;

use Kernel\Routing\Route;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class WrongRoutePatternException extends \Exception {
    /**
     * @var \Kernel\Routing\Route
     */
    private $route;
    public function __construct(Route $route, $message)
    {
        $this->route = $route;
        parent::__construct("$message.  Идентификатор маршрута: ".
            $this->route->getName());
    }
}
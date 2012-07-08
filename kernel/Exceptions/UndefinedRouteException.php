<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class UndefinedRouteException extends \Exception {

    public function __construct($name)
    {
        parent::__construct("Маршрут '$name' отсутствует в системе.");
    }
}
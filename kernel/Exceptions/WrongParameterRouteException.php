<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class WrongParameterRouteException extends \Exception {

    public function __construct($optName, $routeName)
    {
        parent::__construct("Параметр '$optName' является обязательным для" .
        " машрута: " . $routeName);
    }
}
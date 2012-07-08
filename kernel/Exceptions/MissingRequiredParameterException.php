<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class MissingRequiredParameterException extends \Exception {

    public function __construct($param, $controller, $action)
    {
        parent::__construct("Отсутствует значение для обязательного параметра '$param'" .
            " в вызове метода '" . get_class($controller) . "::$action.");
    }
}
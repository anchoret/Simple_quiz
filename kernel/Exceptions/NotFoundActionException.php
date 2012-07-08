<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class NotFoundActionException extends \Exception {

    public function __construct($class, $action)
    {
        parent::__construct("Класс контроллера '$class' найден, но метод '$action'" .
            "отсутствует или недоступен для вызова");
    }
}
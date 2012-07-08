<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class NotFoundControllerException extends \Exception {

    public function __construct($controller)
    {
        parent::__construct("Контроллер '$controller' отсутствует или не наследует базовый.");
    }
}
<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class UndefinedServiceContainerException extends \Exception {

    public function __construct($name)
    {
        parent::__construct('Сервис "' . $name . '" отсутствует в контейнере проекта.');
    }
}
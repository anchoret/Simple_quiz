<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class ErrorServiceConstructException extends \Exception {

    public function __construct($name, $class, $params)
    {
        parent::__construct('Ошибка инициализации сервиса "' . $name .'" ' .
            'new ' . $class . 'с параметрами: ' . implode('; ', $params));
    }
}
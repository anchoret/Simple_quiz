<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class UndefinedContainerParameterException extends \Exception {

    public function __construct($name)
    {
        parent::__construct('Параметр "' . $name . '" отсутствует в контейнере.');
    }
}
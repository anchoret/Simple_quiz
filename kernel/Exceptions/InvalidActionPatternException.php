<?php
namespace Kernel\Exceptions;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class InvalidActionPatternException extends \Exception {

    public function __construct($pattern)
    {
        parent::__construct("Шаблон вызова контроллера некорректный: ".$pattern);
    }
}
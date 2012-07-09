<?php
namespace Kernel\Exceptions;

/**
 * Description of WrongActionReturnParameter
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class WrongActionReturnParameter extends \Exception {
    public function __construct($returnValue)
    {
        parent::__construct(
            'Метод контроллера должен вернуть экземпляр класса Kernel\HTTP\Response,' .
            ' либо его наследника. Метод '.$method.' вернул' . gettype($content));
    }
}

<?php
namespace Kernel\Exceptions;

/**
 * Description of WrongActionReturnParameter
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class WrongActionReturnParameterException extends \Exception {
    public function __construct($returnValue, $method)
    {
        parent::__construct(
            'Метод контроллера должен вернуть экземпляр класса Kernel\HTTP\Response,' .
            ' либо его наследника. Метод '.$method.' вернул ' . gettype($returnValue));
    }
}

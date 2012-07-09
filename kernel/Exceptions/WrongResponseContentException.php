<?php
namespace Kernel\Exceptions;

/**
 * Description of WrongResponseContentException
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class WrongResponseContentException extends \Exception {
    public function __construct($content) {
        parent::__construct('Текст ответа должен быть строкой, числом или классом, ' .
                'реализующим метод __toString. Передан: ' . gettype($content));
    }
}

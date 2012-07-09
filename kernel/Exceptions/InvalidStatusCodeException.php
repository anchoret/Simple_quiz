<?php
namespace Kernel\Exceptions;

/**
 * Description of InvalidStatusCodeException
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class InvalidStatusCodeException extends \Exception {
    public function __construct($code)
    {
        parent::__construct('Некорректный код статуса ответа: ' . $code);
    }
}

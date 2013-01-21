<?php
namespace Kernel\Exceptions;

/**
 * Description of InvalidStatusCodeException
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class InvalidViewFileException extends \Exception {
    public function __construct($path)
    {
        parent::__construct('Файл вида "' . $path . '" не существует, либо недоступен.');
    }
}

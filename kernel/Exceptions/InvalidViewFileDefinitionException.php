<?php
namespace Kernel\Exceptions;

/**
 * Description of InvalidStatusCodeException
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class InvalidViewFileDefinitionException extends \Exception {
    public function __construct()
    {
        parent::__construct('Файл вида задан некорректным образом.');
    }
}

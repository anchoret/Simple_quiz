<?php
namespace Kernel\Exceptions;

use Kernel\Routing\Route;
/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class RouteNotFoundException extends \Exception {

    public function __construct($request_uri)
    {
        parent::__construct("Не найдено маршрутов для запроса: '$request_uri'");
    }
}
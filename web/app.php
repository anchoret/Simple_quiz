<?php
use Kernel\Request;

require_once __DIR__.'/../kernel/Kernel.php';
$kernel = new Kernel\Kernel('dev');
$kernel->boot()->start();

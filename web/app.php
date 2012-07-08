<?php
use Kernel\Request;

require_once __DIR__.'/../kernel/Kernel.php';
$kernel = new Kernel\Kernel('dev');
$kernel->boot();


use QuizModule\Controller\Quiz;
$quiz = new Quiz();
echo $quiz->hello();
//use ;

//$kernel = new AppKernel('prod', false);
//$kernel->loadClassCache();
////$kernel = new AppCache($kernel);
//$kernel->handle(Request::createFromGlobals())->send();

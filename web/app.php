<?php
use Kernel\Request;

require_once __DIR__.'/../kernel/Kernel.php';
$mode = getenv('PROJECT_MODE')?:'dev';
$kernel = new Kernel\Kernel($mode);
$kernel->boot();
try{
    $kernel->start();
} catch (\Exception $e) {
    if ($mode == 'dev'){
        $content = '<div>' . $e->getMessage() . '</div>';
        $content .= '<div><ul>';
        foreach ($e->getTrace() as $call) {
            foreach ($call['args'] as $key => $arg) {
                if (is_object($arg)) {
                    $call['args'][$key] = get_class($arg);
                } elseif(is_array($arg)) {
                    $call['args'][$key] = "array( " . implode(",", $arg) .")";
                }
            }
            $content .= "<li>Вызов " . $call['class'] . $call['type'] . $call['function'] .
                (count($call['args'])>0 ? (' с параметрами: ' . implode('; ', $call['args']))
                    : ' без параметров<br/>') .
                ' в ' . $call['file'] . ' ' . $call['line'] . ';</li>';
        }
        $content .= '</ul></div>';
        $response = new Kernel\HTTP\Response($content);
        echo $response->sendToClient();
    }
}

<?php
namespace Kernel\Routing\Loaders;

use Kernel\Routing\Route;

/**
 * Class for loading routing from ini file.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class IniRouterLoader implements RouterLoadeInterface
{
    public $config;

    public function __construct($file)
    {
        $this->config = $file;
    }
    public function load()
    {
        $routes = array();
        $configArray = parse_ini_file($this->config, true);
        foreach ($configArray as $name => $property) {
            $default = array();
            preg_match_all('/{([a-zA-Z]+)}/', $property['pattern'], $vars);
            foreach ($vars[1] as $var) {
                if (isset($property[$var])) {
                    $default[$var] = $property[$var];
                }
            }
            if (!isset($property['type'])) {
                $property['type'] = 'ALL';
            }
            $routes[$name] = new Route($name,
                $property['pattern'], $property['action'],
                $default, $vars[1], $property['type']);
        }

     return $routes;
    }
}

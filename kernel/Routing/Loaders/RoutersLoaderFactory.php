<?php
namespace Kernel\Routing\Loaders;

/**
 * Implementing factory method pattern.
 * Creating new RouterLoader by extension of routing config file.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class RoutersLoaderFactory
{
    public function getLoader($configFile)
    {
        $extension = strtolower(substr($configFile, strrpos($configFile, '.')+1));
        if ($extension == 'ini'){
            return new IniRouterLoader($configFile);
        } else {
            throw new \Exception("Router loader for file'type '$extension' does
                not implementing in the system");
        }
    }
}

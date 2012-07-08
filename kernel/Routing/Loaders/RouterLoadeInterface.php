<?php
namespace Kernel\Routing\Loaders;

/**
 * All router loaders must implementing this interface;
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
interface RouterLoadeInterface
{
    public function load();
}

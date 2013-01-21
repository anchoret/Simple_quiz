<?php
namespace Kernel\View;

use Kernel\View\Layout;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 *         Date: 22.01.13
 *         Time: 0:44
 */
class TemplateEngine
{
    private $_layout;

    public function __construct(Layout $layout)
    {
        $this->_layout = $layout;
    }

    public function render()
    {
        return $this->_layout->render();
    }
}

<?php
namespace Kernel\Controller;

use Kernel\ServiceContainer\Container;
use Kernel\HTTP\Request;
use Kernel\HTTP\Response;

/**
 * Description of AbstractController
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
abstract class AbstractController {

    private $container = null;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function generateUrl($name, $options = array(), $absolute = false, $https = false)
    {
        return $this->get('kernel.router')
            ->generateURl($name, $options, $absolute, $https);
    }

    public function redirect($url, $status = 302)
    {
       return new Response($url, $status, array('location' => $url));
    }

    public function render($view, array $options = array())
    {
        return new Response($this->get('kernel.template.engine')
            ->render($view, $options));
    }

    protected function get($name)
    {
        return $this->container->get($name);
    }

    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }
}

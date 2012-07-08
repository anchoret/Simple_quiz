<?php

namespace Kernel\HTTP;

class Request
{

    /**
     * @var array
     */
    private $get;

    /**
     * @var array
     */
    private $post;

    /**
     * @var array
     */
    private $server;

    /**
     * @var array
     */
    private $header;

    /**
     * @var array
     */
    private $charset;

    /**
     * @var string
     */
    private $requestURI;

    /**
     * @param array  $get     The GET parameters
     * @param array  $post    The POST parameters
     * @param array  $server  The SERVER parameters
     */
    public function __construct(array $get = array(), array $post = array(), array $server = array())
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->header = $this->_getHeaders($server);
        $this->charset = 'UTF-8';
    }

    protected function _getHeaders($server)
    {
        $headers = array();
        foreach ($server as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[substr($key, 5)] = $value;
            }
        }
    }

    public function bindGlobalVars()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->header = $this->_getHeaders($_SERVER);
    }

    public function getServerVariable($name, $default = false)
    {
        return isset($this->server[$name])?$this->server[$name]:$default;
    }

    public function getRequestURI() {
        if ($this->requestURI == null){
            $this->setRequestURI($this->server['REQUEST_URI']);
        }

        return $this->requestURI;
    }

    protected function setRequestURI($requestURI) {
        $requestURI = urldecode($requestURI);
        $requestURI =
            preg_replace(array('/\s+/', '/\/{2,}/'), array('', '/'), $requestURI);
        $right = strrpos($requestURI, '?');
        $start = ('/' == $requestURI[0] ? 1 : 0);
        if ($right === false) {
            $length = strlen($requestURI)- $start;
        } else {
            $length = $right - 1;
        }
        $uri = substr($requestURI, $start,
            ('/' == $requestURI[$length] ? $length-1 : $length));
        $this->requestURI = $uri;

        return $this;
    }

}

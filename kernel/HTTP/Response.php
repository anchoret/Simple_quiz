<?php
namespace Kernel\HTTP;

use Kernel\Exceptions;

/**
 * Response wrapper for HTTP response.
 *
 * @author Alexey Korolev <alexey.korolev@opensoftdev.com>
 *
 */
class Response
{
    const HTTP_1_1 = 'HTTP/1.1';

    const HTTP_1_0 = 'HTTP/1.0';

    private $headers;
    private $content;
    private $version;
    private $statusCode;
    private $statusText;
    private $charset;

    static public $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * @param string  $content HTTP response's content
     * @param integer $status  HTTP response's code of status
     * @param array   $headers Array of HTTP response's headers
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->headers = $headers;
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion(self::HTTP_1_1);
    }

    /**
     * Debug response
     *
     * @return string The response content
     */
    public function __toString()
    {
        $this->prepareHeaders();

        $headers = '';
        foreach ($this->headers as $name => $value) {
            $headers .= $name.': '.$value . '\r\n';
        }

        return
            sprintf('%s %s %s', $this->version, $this->statusCode, $this->statusText)."\r\n".
            $headers . "\r\n" .
            $this->getContent();
    }

    public function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        $this->prepareHeaders();
        header(sprintf('%s %s %s', $this->version, $this->statusCode, $this->statusText));

        foreach ($this->headers as $name => $value) {
            header($name.': '.$value, false);
        }
    }

    public function sendContent()
    {
        echo $this->getContent();
    }

    /**
     * Send HTTP response.
     */
    public function sendToClient()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    public function prepareHeaders()
    {
        if (($this->statusCode >= 100 && $this->statusCode < 200) ||
            in_array($this->statusCode, array(204, 304))) {

            $this->setContent('');
        }

        $charset = $this->charset ?: 'UTF-8';
        if (!array_key_exists('Content-Type', $this->headers)) {
            $this->headers['Content-Type'] = 'text/html; charset='.$charset;
        } elseif ('text/' === substr($this->headers['Content-Type'], 0, 5) &&
            false === strpos($this->headers['Content-Type'], 'charset')) {

            $this->headers['Content-Type'] = $this->headers['Content-Type'].'; charset='.$charset;
        }
    }

    /**
     * Sets the HTTP response's content.
     *
     * Valid types are strings, numbers, and objects that implement a __toString() method.
     *
     * @param mixed $content
     */
    public function setContent($content)
    {
        if (null !== $content && !is_string($content) &&
            !is_numeric($content) && !is_callable(array($content, '__toString'))) {

            throw new Exceptions\WrongResponseContentException($content);
        }

        $this->content = (string) $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setProtocolVersion($version)
    {

        $this->version = $version == self::HTTP_1_0 ? : self::HTTP_1_1;
    }

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = (int) $code;
        if ($this->statusCode < 100 || $this->statusCode >= 600) {
            throw new Exceptions\InvalidStatusCodeException($code);
        }

        null === $text ? self::$statusTexts[$this->statusCode] : $text;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getCharset()
    {
        return $this->charset;
    }
}
<?php
namespace Kernel\Db;

/**
 * Wrapper class for PDO module functions.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class DbConnector {

    private $driver;
    private $host;
    private $port;
    private $name;
    private $user;
    private $password;
    private $charset;

    private static $connect = null;

    public function __construct($driver, $host, $port, $name, $user, $password, $charset)
    {
        $this->driver  = $driver;
        $this->host    = $host;
        $this->port    = $port;
        $this->name    = $name;
        $this->user    = $user;
        $this->pass    = $password;
        $this->charset = $charset;
    }

    public function getConnection()
    {
        if (static::$connect === null){
            $dsn = $this->driver . ':dbname=' . $this->name . ';host=' . $this->host .
                ';port=' . $this->port;

        static::$connect = new \PDO($dsn, $this->user, $this->pass,
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . $this->charset . '\''));
        }

        return static::$connect;
    }
}

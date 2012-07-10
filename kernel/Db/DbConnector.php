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

    private $openTransaction = false;

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

    public function beginTransaction(){
        if($openTransaction === false) {
           return $openTransaction = $this->getConnection()->beginTransaction();
        }

        return false;
    }
    public function commitTransaction()
    {
        if($openTransaction === false) {
           return $openTransaction = $this->getConnection()->commit();
        }

        return false;
    }
    public function rollbackTransaction()
    {
        if($openTransaction === false) {
           return $openTransaction = $this->getConnection()->rollBack();
        }

        return false;
    }

    public function errorCode()
    {
        return $this->getConnection()->errorCode();
    }
    public function errorInfo()
    {
        return $this->getConnection()->errorInfo();
    }
    public function execute($statement)
    {
        return $this->getConnection()->exec($statement);
    }
    public function getAttribute(integer $attr)
    {
        return $this->getConnection()->getAttribute($attr);
    }
    public function getAvailableDrivers()
    {
        return $this->getConnection()->getAvailableDrivers();
    }
    public function inTransaction()
    {
        return $this->getConnection()->inTransaction();
    }
    public function lastInsertId($name = null)
    {
        if ($name === null) {
            return $this->getConnection()->lastInsertId();
        } else {
            return $this->getConnection()->lastInsertId($name);
        }
    }
    public function prepare($statement, $options = array())
    {
        return $this->getConnection()->prepare($statement, $options = array());
    }
    public function query($statement)
    {
        return $this->getConnection()->query($statement);
    }
    public function quote($string)
    {
        return $this->getConnection()->quote($string);
    }
    public function setAttribute($attribute , $value)
    {
        return $this->getConnection()->setAttribute($attribute , $value);
    }
}

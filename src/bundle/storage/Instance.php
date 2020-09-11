<?php

class Instance {
    private $strHost = "127.0.0.1";
    private $intPort = 6379;
    private $strPassword = "";
    private $intDataBase = 0;

    private $objRedis = null;

    public function __construct($host, $port, $password, $database) {
        $this->strHost = $host;
        $this->intPort = $port;
        $this->strPassword = $password;
        $this->intDataBase = $database;
    }


    public function setHost( $value) {
        $this->strHost = $value;
    }
    public function setPort( $value) {
        $this->intPort = $value;
    }

    public function setPassword( $value) {
        $this->strPassword = $value;
    }

    public function setDataBase( $value) {
        $this->intDataBase = $value;
    }


    public function connect() {
        $this->objRedis = new Redis();
        try {
            $this->objRedis->connect($this->strHost, $this->intPort);
            false == empty($this->strPassword) or $this->objRedis->auth($this->strPassword);
            false == empty($this->intDatabase) or $this->objRedis->select($this->intDataBase);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return true;
    }

    public function __call($name, $arguments) {

        return call_user_func_array(array($this->objRedis, $name), $arguments);
    }
}

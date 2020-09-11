<?php

class Topic {

    private $topic = "";

    private $file = "";

    private $class = "";

    private $method = "";

    private $wait_time = 1000;

    public function __construct($topic, $file, $class, $method, $wait_time = 1000) {
        $this->topic = $topic;
        $this->file = $file;
        $this->class = $class;
        $this->method = $method;
        $this->wait_time = $wait_time;
    }

    public function __set($name, $value) {
        // TODO: Implement __set() method.
        $this->$name = $value;
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        return $this->$name;
    }
}

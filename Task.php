<?php
class Task  {
    private $task_id = 0;
    private $topic = "";
    private $is_sync = true;
    private $wait_time = 1000;
    private $exec_time = 0;
    private $start_time = 0;
    private $data = [];

    public function __construct($topic = "", $is_sync = true, $wait_time = 1000, $data = []) {
        $this->task_id = $this->uuid();
        $this->topic = $topic;
        $this->is_sync = $is_sync;
        $this->wait_time = $wait_time;

        $this->data = $data;
    }

    public function __set($name, $value) {
        switch ($name) {
            case 'topic':
                $this->topic = strval($value);
                break;
            case 'is_sync':
                $this->is_sync = boolval($value);
                break;
            case 'wait_time':
                $this->wait_time = intval($value);
                break;
            case 'data':
                $this->data = $value;
                break;
            case 'exec_time':
                $this->exec_time = $value;
                break;
            case 'start_time':
                $this->start_time = $value;
                break;
        }
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        return $this->$name;
    }

    private function uuid() {
        $chars = md5(uniqid(microtime(true) * 10 + mt_rand(0, 9), true));
        $uuid = substr($chars, 0, 8 ) . '-' . substr ($chars, 8, 4) . '-' . substr ($chars, 12, 4 ) . '-' . substr ($chars, 16, 4) . '-' . substr ($chars, 20, 12);
        return $uuid ;
    }

}

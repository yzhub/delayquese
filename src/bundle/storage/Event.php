<?php

class Event {

    private  $objServer = [];

    public function __construct() {
        $this->objServer = [];
    }

    /**
     * 注册key
     * @param $key
     * @param $value
     */
    public function register($key, $value) {
        if(@false == isset($this->objServer[$key])) {
            $this->objServer[$key] = $value;
            return true;
        }
        return false;
    }

    /**
     * 移除
     * @param $key
     */
    public function remove($key) {
        if(@true == isset($this->objServer[$key])) {
            unset($this->objServer[$key]);
            return true;
        }
        return false;
    }

    /**
     * 消息通知
     * @param $method
     * @param $arguments
     */
    public function notify($method, $arguments) {
        foreach ($this->objServer as $ObServer) {
            $ObServer->connect();
            $data[] = json_encode( call_user_func_array(array($ObServer, $method), $arguments));
        }
        return array_map(function($v){return json_decode($v, true);},  array_unique($data))[0];
    }

}

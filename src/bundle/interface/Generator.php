<?php
interface Generator {

    /**
     * 注册key
     * @param $key
     * @param $value
     */
    public function register($key, $value);

    /**
     * 移除
     * @param $key
     */
    public function remove($key);

    /**
     * 消息通知
     * @param $method
     * @param $arguments
     */
    public function notify($method, $arguments);
}

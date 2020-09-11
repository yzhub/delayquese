<?php
$GLOBALS['DELAY_QUEUE_ROOT'] = dirname(__FILE__).'/src';
include $GLOBALS['DELAY_QUEUE_ROOT'] . "/bundle/storage/Instance.php";
include $GLOBALS['DELAY_QUEUE_ROOT'] . "/bundle/storage/Event.php";
include $GLOBALS['DELAY_QUEUE_ROOT'] . "/bundle/storage/Topic.php";
include $GLOBALS['DELAY_QUEUE_ROOT'] . "/bundle/model/Shm.php";
include $GLOBALS['DELAY_QUEUE_ROOT'] . "/operation/Dispatch.php";
include $GLOBALS['DELAY_QUEUE_ROOT'] . "/operation/Execute.php";
class DelayQueue {

    const TASK_SORTED_QUEUE = 'task_sorted_queue';

    public static $objStorageEvent = null;

    /**
     * 设置存储配置信息
     * @param $host
     * @param $port
     * @param string $password
     * @param int $database
     */
    public static function setStorageConfig($host, $port, $password = '', $database = 0) {
        if(self::$objStorageEvent == null) {
            self::$objStorageEvent = new Event();
        }
        $key = md5(sprintf("%s:%s:%s:%s",$host, $port, $password, $database));
        $objRedis = new Instance($host, $port, $password, $database);
        self::$objStorageEvent->register($key, $objRedis);
    }

    /**
     * 设置Topic配置信息
     * @param $topic
     * @param $file
     * @param $class
     * @param $method
     */
    public static function setTopicConfig($topic, $file, $class, $method) {
        $objShm = new Shm();
        $objShm->write($topic, new Topic($topic, $file, $class, $method));
    }

    private function getMessage($item) {
        return [
            'create_time' => microtime(true),
            'exec_time' => false == $item->is_sync ? microtime(true) + $item->wait_time : microtime(),
            'is_sync' => $item->is_sync,
            'task_id' => $item->task_id
        ];
    }


    public static function task($item) {
        //  检查topic 是否存在
        $objShm = new Shm();
        if(false == $objShm->find($item->topic)) {
            return false;
        }
        // 加入到Redis存储队列
        self::$objStorageEvent->notify('set',[$item->task_id, serialize($item)]);
        $item->start_time = intval(microtime(true) * 1000);
        $item->exec_time = false == $item->is_sync ? $item->start_time + $item->wait_time : $item->start_time;
        // 加入待执行任务列表
        return (new Dispatch())->addReadyTask($item);
    }

    /**
     * 监听任务队列
     */
    public static function listion() {
        while (true) {
            (new Dispatch())->execute();
            usleep(100);
        }
    }
}

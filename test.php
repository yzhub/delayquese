<?php

include_once("DelayQueue.php");
include_once("Task.php");

class Test {

    public function __construct() {


        $redis_1 = ['host' => '127.0.0.1', 'port' => 6379, 'password' => '', 'database'=>0];
        $redis_2 = ['host' => '127.0.0.1', 'port' => 6379, 'password' => '', 'database'=>1];
        $redis_3 = ['host' => '127.0.0.1', 'port' => 6379, 'password' => '', 'database'=>2];

        $topic_1 = ['topic' => 'a', "file" => "controllers/commCtrl/CallbackController.php", "class" => "", "method" => ""];
        $topic_2 = ['topic' => 'b', "file" => "controllers/commCtrl/CallbackController.php", "class" => "", "method" => ""];


// 设置Redis存储信息
        DelayQueue::setStorageConfig($redis_1['host'], $redis_1['port'], $redis_1['password'], $redis_1['database']);
        DelayQueue::setStorageConfig($redis_2['host'], $redis_2['port'], $redis_2['password'], $redis_2['database']);
        DelayQueue::setStorageConfig($redis_3['host'], $redis_3['port'], $redis_3['password'], $redis_3['database']);


// 设置 Topic配置信息
        DelayQueue::setTopicConfig($topic_1['topic'], $topic_1['file'], $topic_1['class'], $topic_1['method']);
        DelayQueue::setTopicConfig($topic_2['topic'], $topic_2['file'], $topic_2['class'], $topic_2['method']);

    }


    public function newTask() {
        // 新建任务1
        $t1 = new Task();
        $t1->topic = 'a';
        $t1->data = ['1','2','3'];
        $t1->wait_time = 1000;
        $t1->is_sync = false;
        // 新建任务2
        $t2 = new Task();
        $t2->topic = 'a';
        $t2->data = ['1','2','3'];
        $t2->wait_time = 2000;
        $t2->is_sync = false;
        // 新建任务3
        $t3 = new Task();
        $t3->topic = 'a';
        $t3->data = ['1','2','3'];
        $t3->wait_time = 3000;
        $t3->is_sync = false;


        DelayQueue::task($t1);
        DelayQueue::task($t2);
        DelayQueue::task($t3);
    }


    public function scriptListen() {
        DelayQueue::listion();
    }

    public  function process($type)  {
        switch ($type) {
            case 'a':
                $this->newTask();
                break;
            case 'b':
                $this->scriptListen();;
                break;
        }
    }
}

(new Test())->process($argv[1]);









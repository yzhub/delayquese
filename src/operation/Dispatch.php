<?php

class Dispatch {


    /**
     * 添加任务
     * @param $objTask
     * @return bool
     */
    public function addReadyTask($objTask) {
        $isExists = $this->taskExists($objTask->task_id);
        if(true == $isExists) {
            return false;
        }
        return $this->addTaskQueue($objTask);
    }

    public function execute() {
        $ret = $this->getExecQueue(microtime(true));
        if(false == $ret) {
            return false;
        }
        foreach ($ret as $key => $value) {
            // 获取相关任务信息， 获取topic 信息，组合调用包 调用

            // 从队列中删除任务以及，任务详细信息
        }
    }

    /**
     * 判断任务队列是否存在
     * @param $task_id
     * @return bool
     */
    private function taskExists($task_id) {
        try {
            $ret = DelayQueue::$objStorageEvent->notify('zrank', [DelayQueue::TASK_SORTED_QUEUE, $task_id]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * 添加任务队列到有序集合
     * @param $objTask
     * @return bool
     */
    private function addTaskQueue($objTask) {
        try {
            $ret = DelayQueue::$objStorageEvent->notify('zadd', [DelayQueue::TASK_SORTED_QUEUE, $objTask->exec_time, $objTask->task_id]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * 获取任务队列
     * @param $endTime
     * @return bool
     */
    private function getExecQueue($endTime) {
        try {
            $ret = DelayQueue::$objStorageEvent->notify('zrange', [DelayQueue::TASK_SORTED_QUEUE, 0, -1]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return $ret;
    }
}


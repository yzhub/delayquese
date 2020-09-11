<?php

class Dispatch {

    /**
     * 添加任务
     * @param $objTask
     * @return bool
     */
    function addReadyTask($objTask) {
        $isExists = $this->taskExists($objTask->task_id);
        if(true == $isExists) {
            return false;
        }
        return $this->addTaskQueue($objTask);
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
            $ret = DelayQueue::$objStorageEvent->notify('zadd', [DelayQueue::TASK_SORTED_QUEUE, 0, $endTime]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return $ret;
    }
}


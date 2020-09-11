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
        $arrTaskList = $this->getExecQueue( intval(microtime(true) * 1000));
        if(false == $arrTaskList) {
            return false;
        }
        $arrTaskList =  array_chunk($arrTaskList, 100);
        // 数据打包
        foreach ($arrTaskList as $item) {
            foreach ($item as $node) {
                // 获取相关任务信息， 获取topic 信息，组合调用包 调用
                $objTask = $this->getTaskInfo($node);
                if(false == $objTask) {
                    continue;
                }
                $objTopic = $this->getTopicInfo($objTask->topic);
                if(false == $objTopic) {
                    continue;
                }
                // 执行数据
                (new Execute())->run($objTopic, $objTask);

                // 从队列中删除任务以及，任务详细信息
                $this->delTaskQueus($node);
                $this->delTaskInfo($node);
            }
            usleep(100);
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
            $ret = DelayQueue::$objStorageEvent->notify('zrangebyscore', [DelayQueue::TASK_SORTED_QUEUE, 0, $endTime]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return $ret;
    }

    /**
     * 获取任务详细信息
     * @param $taskId
     */
    public function getTaskInfo($taskId) {
        try {
            $ret = DelayQueue::$objStorageEvent->notify('get', [$taskId]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return unserialize($ret);
    }

    /**
     * 获取topicId 信息
     * @param $topicId
     */
    public function getTopicInfo($topicId) {
        $objShm = new Shm();
        if(false == $objShm->find($topicId)) {
            return false;
        }
        return $objShm->read($topicId);
    }


    public function delTaskInfo($taskId) {
        try {
            $ret = DelayQueue::$objStorageEvent->notify('del', [$taskId]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    public function delTaskQueus($taskId) {
        try {
            $ret = DelayQueue::$objStorageEvent->notify('zdelete', [DelayQueue::TASK_SORTED_QUEUE, $taskId]);
            if(false == $ret) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}


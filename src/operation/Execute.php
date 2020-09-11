<?php
class Execute {
    public function run($objTopic, $objTask) {
        echo sprintf("正在执行任务: %s", $objTask->task_id) .PHP_EOL;
    }
}

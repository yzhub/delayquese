<?php
class Shm
{
    private $blockSize = 512; // 块的大小(byte)
    private $memSize = 25600; // 最大共享内存(byte)
    private $shmId = 0;
    private $semId = 0;

    public function __construct () {
        $shmkey = ftok(__FILE__, 't');
        $this->shmId = shmop_open($shmkey, "c", 0644, $this->memSize);
        $this->maxQSize = $this->memSize / $this->blockSize;
        // 申請一个信号量
        $this->semId = sem_get($shmkey, 1);
        // 申请进入临界区
        sem_acquire($this->semId);
    }

    private function getMemeData() {
        $data = shmop_read($this->shmId, 0, $this->blockSize - 1);
        return $this->decode($data);
    }

    private function setMemData($data) {
        shmop_write($this->shmId, $this->encode($data), 0);
        return true;
    }

    public function read($key) {
        $arrMemData = $this->getMemeData();
        if(true  == is_null($arrMemData[$key])) {
            return false;
        }
        return $arrMemData[$key];
    }

    private function encode ($value) {
        $data = serialize($value) . "__eof";
        if (strlen($data) > $this->blockSize - 1) {
            throw new Exception(strlen($data) . " is overload block size!");
        }
        return $data;
    }

    private function decode ($value) {
        $data = explode("__eof", $value);
        return unserialize($data[0]);
    }

    public function find($key) {
        $data = $this->getMemeData();
        if(true == isset($data[$key])) {
            return true;
        }
        return false;
    }

    public function write($key, $value) {
        $data = $this->getMemeData();
        $data[$key] = $value;
        $this->setMemData($data);
        return true;
    }

    public function delete($key) {
        $data = $this->getMemeData();
        if(true == is_null($data[$key])) {
          return false;
        }
        unset($data[$key]);
        $this->setMemData($data);
        return true;
    }

    public function __destruct() {
        sem_release($this->semId); // 出临界区, 释放信号量
    }
}

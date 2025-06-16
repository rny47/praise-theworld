<?php

namespace TaskManager;

use Illuminate\Support\Facades\Redis;

class TaskCore
{
    public $strQueueKey = 'QueueKey';

    public function __construct($strQueueKey = "")
    {
        if ($strQueueKey != "") {
            $this->strQueueKey = $strQueueKey;
        } else {
            $this->strQueueKey = config('task.queue_key', 'QueueKey');
        }
    }

    public function get()
    {
        return Redis::connection('cache')->lpop($this->strQueueKey);
    }

    public function set($arrData)
    {
        $strData = json_encode($arrData);
        return Redis::connection('cache')->rpush($this->strQueueKey, $strData);
    }

    public function llen()
    {
        return Redis::connection('cache')->llen($this->strQueueKey);
    }
}

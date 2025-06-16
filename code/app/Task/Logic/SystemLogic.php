<?php

namespace App\Task\Logic;

use \Illuminate\Support\Facades\Redis;
use \Illuminate\Support\Facades\DB;

class SystemLogic
{
    /**
     * 销毁连接资源
     *
     * @return bool
     */
    static public function disconnect()
    {
        DB::disconnect();
        Redis::disconnect();
        return true;
    }
}

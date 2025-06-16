<?php

namespace App\Task\Core;

use App\Task\Logic\SystemLogic;


class ScheduledTasks
{
    static public function hander($arrProcessTask = [])
    {
        SystemLogic::disconnect();
        //  while(true){
        call_user_func($arrProcessTask['callback'], $arrProcessTask['param']);

        sleep($arrProcessTask['execution_interval']);
        // }

        // \Swoole\Timer::tick($arrProcessTask['execution_interval']*1000, $arrProcessTask['callback'], $arrProcessTask['param']);

        // \Swoole\Event::wait();
    }
}

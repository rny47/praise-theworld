<?php

namespace App\Task\Module;

use App\Task\Logic\SystemLogic;
use Cache;
use Swoole\Http\Server;
use Swoole\Process;
use TaskManager\TaskCore;

class TaskListen
{
    /**
     * 回调函数
     *
     * @param Server $Server
     * @param Process $Process
     * @return void
     */
    static public function innerConsume(\Swoole\Process $Process)
    {
        // SystemLogic::disconnect();
        $intLimit = 100;
        $TaskCore = new TaskCore();
        while ($intLimit > 0) {
            $intLimit--;
            try {
                $strData = $TaskCore->get();
                if (empty($strData)) {
                    sleep(1);
                    continue;
                }
                $arrData = json_decode($strData, true);
                if (!is_array($arrData)) {
                    echo '监听Redis队列读取到异常无法解析的数据[' . $strData . ']' . PHP_EOL;
                    continue;
                }
                $Class = new $arrData['callback'][0];
                $Class->{$arrData['callback'][1]}($arrData['data']);
            } catch (\Throwable $t) {
                // print_r($t->getMessage());
                debugErr($t, '监听Redis发现错误', $strData);
            }
        }
    }
}

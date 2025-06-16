<?php

namespace App\Task\Module;

use TaskManager\RobotCore;
use TaskManager\TaskCore;

class SystemCheck
{
    /**
     * 将发布视频的消息推送给电报
     *
     * @return void
     */
    public function checkSystemHealth()
    {
        try {

            $TaskCore = new TaskCore();
            $RobotMsgManager = new RobotMsgManager;

            $strAppName = config('app.name');

            $strContent = sprintf(
                "健康检测时间： %s \n" .
                    "CPU使用率:  %s %% \n" .
                    "内存使用率:  %s %% \n" .
                    "待处理任务数:  %s \n" .
                    "平台:  %s \n",
                date('Y-m-d H:i:s'),
                $this->getCpuUsage(),
                $this->getMemoryUsage(),
                $TaskCore->llen(),
                $strAppName
            );

            $arrMsg = ['msg' => $strContent, 'type' => 'success'];

            $RobotMsgManager->sendCommonMsg($arrMsg);

            return true;
        } catch (\Exception $e) {
            debugErr($e, '发送电报消息失败！');
            // throw $e;
        }
        return true;
    }

    /**
     * CPU USE
     *
     * @return int
     */
    public function getCpuUsage()
    {
        $arrSytemInfo = sys_getloadavg();
        return (ceil($arrSytemInfo[0] * 100)) / 100;
    }


    /**
     * Memm Use
     *
     * @return int
     */
    public function getMemoryUsage()
    {
        $intFree = shell_exec('grep MemFree /proc/meminfo | awk \'{print $2}\'');
        $intTotal = shell_exec('grep MemTotal /proc/meminfo | awk \'{print $2}\'');

        $intFree = (int)$intFree;
        $intTotal = (int)$intTotal;
        $intUsed = $intTotal - $intFree;

        return (ceil(($intUsed / $intTotal) * 100 * 100)) / 100;
    }
}

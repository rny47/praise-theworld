<?php

namespace App\Task\Module;

use App\Models\Admin\Configs;
use Log;
use TaskManager\RobotCore;

use function Psy\debug;

class Robot
{
    /**
     * 将发布视频的消息推送给电报
     *
     * @param array $arrVideo
     * @return void
     */
    public function sendMsgToTelegram($arrVideo)
    {
        try {
            $RobotCore =  new RobotCore();
            $RobotMsgManager = new RobotMsgManager;
            while (true) {
                $strMsg = $RobotCore->get();
                if (empty($strMsg)) {
                    sleep(1);
                    return;
                }

                $arrMsg = json_decode($strMsg, true);

                $RobotMsgManager->sendCommonMsg($arrMsg);
            }
            return true;
        } catch (\Exception $e) {
            debugErr($e, '发送电报消息失败！');
            // throw $e;
        }
        return true;
    }
}

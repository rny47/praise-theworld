<?php

namespace Tests\Feature;

use App\Task\Module\RobotMsgManager;
use App\Task\Module\SystemCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use TaskManager\RobotCore;
use TaskManager\TaskCore;
use Tests\TestCase;

class ApkSignerTest extends TestCase
{
    /**
     * 测试发送电报
     *
     * @return void
     */
    public function testSendMsg()
    {

        // $RobotMsgManager = new RobotMsgManager;

        // $arrMsg = [
        //     'type' => 'success',
        //     'msg' => 'test proxy send msg',
        // ];
        // $RobotMsgManager->sendCommonMsg($arrMsg);

        // return  1;

        // $RobotCore = new RobotCore();

        // // $RobotCore->sendNoticMsg('测试!!!');

        // $strTelegramErrorMsg = sprintf(
        //     "时间:[%s]" . PHP_EOL .
        //         "应用名称: [%s]" . PHP_EOL .
        //         "打包指令: [%s]" . PHP_EOL .
        //         "错误: [%s]" . PHP_EOL .
        //         "处理人: [%s]" . PHP_EOL,
        //     date('Y-m-d H:i:s'),
        //     'test app',
        //     'test cmd',
        //     "未正常打包，日志过大，请自行请求日志文件查看具体错误",
        //     '@spark_ph',
        // );
        // $RobotCore->sendErrorMsg($strTelegramErrorMsg);

        $TaskCore = new TaskCore();

        $arrTask = [
            'callback' => [SystemCheck::class, 'checkSystemHealth'],
            'data' => []
        ];

        $TaskCore->set($arrTask);

        $this->assertIsBool(true);
    }
}

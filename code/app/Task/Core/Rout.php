<?php


namespace App\Task\Core;

use App\Task\Logic\SystemLogic;

class Rout
{
    static public function httpDispense(\Swoole\Http\Request $Request, \Swoole\Http\Response $Response)
    {
        SystemLogic::disconnect();
        //  print_r($Request->getData());
        //   print_r($Request->server);

        # 简单的HTTP服务
        $Response->status(999, 'Hei Guys ~');
        $Response->header("Content-Type", "text/html; charset=utf-8");
        $Response->end("<h1>Hello reptile~. #" . rand(1000, 9999) . "</h1>");
    }

    static public function tcpDispense(\Swoole\Server $server, $fd, $reactor_id, $mixedData)
    {
        # 简单的HTTP服务
        print_r($mixedData);
    }

    static public function test()
    {
        var_dump(date('Y-m-d H:i:s'));
        return;
    }
}

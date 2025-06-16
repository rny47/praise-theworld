<?php

namespace TaskManager;
use Illuminate\Support\Facades\Redis;
class RobotCore
{
    private $strRobotToken;

    private $strRobotChatId;

    public $strQueueKey = 'RobotKey';

    public function __construct($strQueueKey = "")
    {
        if ($strQueueKey != "") {
            $this->strQueueKey = $strQueueKey;
        }
    }

    public function getRobotToken()
    {
        return  $this->strRobotToken;
    }

    public function getRobotChatId()
    {
        return  $this->strRobotChatId;
    }

    public function setRobotToken($strRobotToken)
    {
        $this->strRobotToken = $strRobotToken;
    }

    public function setRobotChatId($strRobotChatId)
    {
        $this->strRobotChatId = $strRobotChatId;
    }

    public function get()
    {
        return Redis::connection('cache')->lpop($this->strQueueKey);
    }

    public function set($arrData)
    {
        $strData = json_encode($arrData);
        Redis::connection('cache')->rpush($this->strQueueKey, $strData);
        return true;
    }

    public function sendSuccessMsg($strMsg)
    {
        $arrData = [
            'msg' => $strMsg,
            'type' => 'success',
        ];
        return  $this->set($arrData);
    }

    public function sendErrorMsg($strMsg)
    {
        $arrData = [
            'msg' => $strMsg,
            'type' => 'error',
        ];
        return  $this->set($arrData);
    }

    public function sendWarnMsg($strMsg)
    {
        $arrData = [
            'msg' => $strMsg,
            'type' => 'warn',
        ];
        return  $this->set($arrData);
    }

    public function sendNoticMsg($strMsg)
    {
        $arrData = [
            'msg' => $strMsg,
            'type' => 'notice',
        ];
        return  $this->set($arrData);
    }
}

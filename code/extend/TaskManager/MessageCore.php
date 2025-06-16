<?php

namespace TaskManager;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
class MessageCore
{
    public $strMsgQueueKey = '';

    public function __construct($strMsgId = '')
    {
        if ($strMsgId == '') {
            $this->strMsgQueueKey = Str::uuid();
        } else {
            $this->strMsgQueueKey = $strMsgId;
        }
    }

    public function getMsgQueueKey()
    {
        return $this->strMsgQueueKey;
    }

    public function get()
    {
        return Redis::connection('cache')->lpop($this->strMsgQueueKey);
    }

    public function set($arrData)
    {
        $strData = json_encode($arrData);
        Redis::connection('cache')->rpush($this->strMsgQueueKey, $strData);
        Redis::connection('cache')->expire($this->strMsgQueueKey, 24 * 3600);
        return true;
    }

    public function sendStartMsg($strMsg)
    {
        $arrData = [
            'status' => 0,
            'msg' => $strMsg,
        ];
        $this->set($arrData);
    }

    public function sendMsg($strMsg)
    {
        $arrData = [
            'status' => 1,
            'msg' => $strMsg,
        ];
        $this->set($arrData);
    }

    public function sendEndMsg($strMsg)
    {
        $arrData = [
            'status' => 2,
            'msg' => $strMsg,
        ];
        $this->set($arrData);
    }

    public function sendWarningMsg($strMsg)
    {
        $arrData = [
            'status' => 98,
            'msg' => $strMsg,
        ];
        $this->set($arrData);
    }

    public function sendErrorMsg($strMsg)
    {
        $arrData = [
            'status' => 99,
            'msg' => $strMsg,
        ];
        $this->set($arrData);
    }
}

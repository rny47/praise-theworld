<?php

namespace App\Task\Module;

use App\Models\AdminConfigModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

class RobotMsgManager
{
    private $strRobotToken;

    private $strRobotChatId;

    private $Client;

    private $strTelegramUrl = 'https://api.telegram.org';

    public $arrType = [
        'success' => "✅🏆🎉成功",
        'error' => "🚫🚨🔥错误",
        'warn' => "⚠️💢❗警告",
        'notice' => "ℹ️💡📢提示",
    ];

    public function __construct()
    {
        //     $strRobotToken = config('telegram.mybot.token');

        //     $strRobotChatId = config('telegram.mybot.chat_id');

        $this->strTelegramUrl = config('task.telegram_host');
        // echo  $this->strTelegramUrl ;exit;

        $strRobotToken = AdminConfigModel::getAcValByAcCode('telegram_token');
        $strRobotChatId = AdminConfigModel::getAcValByAcCode('telegram_chat_id');

        $this->setRobotToken($strRobotToken);
        $this->setRobotChatId($strRobotChatId);
        $this->Client =  new Client([
            'base_uri' => $this->strTelegramUrl,
            'verify' => false,
        ]);
    }

    public function sendMediaGroup($arrMedia)
    {
        try {
            $strUrl = '/bot%s/sendMediaGroup';
            $strUrl = sprintf($strUrl, $this->strRobotToken);

            $arrSend = [
                'chat_id' => $this->strRobotChatId,
                'media' => json_encode($arrMedia),
            ];


            $Response = $this->Client->post($strUrl, [
                RequestOptions::FORM_PARAMS => $arrSend,
            ]);



            // $Request = new Request('POST', $strUrl, [
            //     'Content-Type' => 'application/x-www-form-urlencoded',
            // ], http_build_query($arrSend));

            // $Response = $this->Client->sendRequest($Request);

            if ($Response->getStatusCode() === 200) {
                return true;
            }
            echo '发送消息失败';
            return false;
        } catch (RequestException $e) {
            echo '发送消息失败，报错：' . $e->getMessage();
            return false;
        }
    }

    /**
     * 真正发送消息的方法
     *
     * @param array $arrSend
     * @return void
     */
    public function  sendMsgCore($arrSend)
    {
        try {
            $strUrl = '/bot%s/sendMessage';
            $strUrl = sprintf($strUrl, $this->strRobotToken);

            $Response = $this->Client->post($strUrl, [
                RequestOptions::FORM_PARAMS => $arrSend,
            ]);


            // $Request = new Request('POST', $strUrl, [
            //     'Content-Type' => 'application/x-www-form-urlencoded',
            // ], http_build_query($arrSend));

            // $Response = $this->Client->sendRequest($Request);

            if ($Response->getStatusCode() === 200) {
                return true;
            }
            echo '发送消息失败';
            return false;
        } catch (RequestException $e) {
            echo '发送消息失败，报错：' . $e->getMessage();
            return false;
        }
    }

    /**
     * 发送菜单消息
     *
     * @param string $strMsg
     * @param array $arrMenu
     * @return void
     */
    public function sendMenu($strMsg, $arrMenu)
    {
        # TODO 存在问题，后期使用需要修改！
        $strMenu = json_encode(['inline_keyboard' => [$arrMenu]]);

        $arrSend = [
            'chat_id' => $this->strRobotChatId,
            'text' => $strMsg,
            'reply_markup' => $strMenu,
        ];

        return $this->sendMsgCore($arrSend);
    }


    /**
     * 发送普通消息
     *
     * @param string $strRobotMsg
     * @return void
     */
    public function sendCommonMsg($arrMsg)
    {
        $strMsg = sprintf("%s: \n %s", $this->arrType[$arrMsg['type']], $arrMsg['msg']);

        $arrSend = [
            'chat_id' => $this->strRobotChatId,
            'text' => $strMsg,
        ];

        return $this->sendMsgCore($arrSend);
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
}

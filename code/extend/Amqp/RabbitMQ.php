<?php

namespace Amqp;

class RabbitMQ
{
    static public $arrCodeData = [
        '1002001' => ['code' => '1002001', 'msg' => '连接失败！', 'data' => ''],
        '1002002' => ['code' => '1002002', 'msg' => '队列获取失败！', 'data' => ''],
    ];

    static private $obj = NULL;

    private $arrConfig = [];

    private $AMQP;

    private $Channel;

    private $Exchange;

    private $Queue;


    static public function instance($arrRabbitConf = []): RabbitMQ
    {
        if (empty($arrRabbitConf)) {
            $arrRabbitConf = config('zm.rabbitmq');
        }

        if (self::$obj == NULL) {
            self::$obj = new static($arrRabbitConf);
        }
        return self::$obj;
    }

    private function __construct($arrAMQPConf)
    {
        //第一步、创建AMQP对象、并且连接
        $this->arrConfig = $arrAMQPConf;
        $AMQP = new \AMQPConnection($arrAMQPConf);
        if (!$AMQP->connect()) {
            throw new \Exception(self::$arrCodeData['1002001']['msg'], self::$arrCodeData['1002001']['code']);
        }
        //第二步、创建通道
        $Channel = new \AMQPChannel($AMQP);

        //第三步、创建交换机
        $Exchange = new \AMQPExchange($Channel);
        $Queue = new \AMQPQueue($Channel);

        foreach ($arrAMQPConf['exchange'] as $strExchange => $arrQueue) {
            $Exchange->setName($strExchange); //创建名字
            $Exchange->setType(AMQP_EX_TYPE_DIRECT);
            $Exchange->setFlags(AMQP_DURABLE);
            $Exchange->declareExchange();
            foreach ($arrQueue as $strKey => $strQueue) {
                //第四步、绑定队列
                $Queue->setName($strQueue);
                $Queue->setFlags(AMQP_DURABLE);
                $Queue->declareQueue();
                $Queue->bind($strExchange, $strKey);
            }
        }

        $this->AMQP = $AMQP;
        $this->Channel = $Channel;
        $this->Exchange = $Exchange;
        $this->Queue = $Queue;
    }

    public function __destruct()
    {
        $this->AMQP->disconnect();
    }

    /**
     * @info 向指定交换机下面的队列推送数据
     * @Author QuickFK
     * @Wanring  正常数据推送流程为指定交换机，指定路由键值，发送数据，但是这样过于复杂，所以采取根据一些参数从配置里读取出来
     * @param unknown $arrData
     * @param string $strExchange
     * @param string $strKeyOrKey
     */
    public function sendData($arrData, $strKeyOrQueue)
    {
        $arrExchangeConf = $this->searchQueue($strKeyOrQueue);

        $strMessage = json_encode($arrData);

        $this->Exchange->setName($arrExchangeConf['exchange']);
        $this->Exchange->setType(AMQP_EX_TYPE_DIRECT);
        $this->Exchange->setFlags(AMQP_DURABLE);
        $this->Exchange->declareExchange();

        return $this->Exchange->publish($strMessage, $arrExchangeConf['key']);
    }

    /**
     * @info 监听一个队列
     * @param  $strKeyOrQueue  队列或键名
     * @param  $Callback	监听回调函数
     * @param  $strErrCallback  回调致命异常后对数据的处理,对重要的操作建议对数据处理一下
     */
    public function Consume($strKeyOrQueue, callable $Callback, $strErrCallback = NULL)
    {
        $arrExchangeConf = $this->searchQueue($strKeyOrQueue);

        $this->Queue->setName($arrExchangeConf['queue']);
        $this->Queue->setFlags(AMQP_DURABLE);
        $this->Queue->declareQueue();

        $this->Queue->consume(function ($Envelope, $Queue) use ($Callback, $strErrCallback) {

            try {
                $strMessge = $Envelope->getBody();

                $arrMessage = json_decode($strMessge, true);

                $arrResult = call_user_func($Callback, [$arrMessage]);

                $Queue->ack($Envelope->getDeliveryTag());
            } catch (\Throwable $t) {
                $this->errorExcuteQueue($arrMessage, $t);
            }
        });
    }

    /**
     * @info 手动拉取消息自动应答模式，解决连接断开问题
     * @param unknown $strKeyOrQueue 队列名
     * @param number $intTasksNum   执行任务个数终止进程
     * @param number $intLifeTime   进程执行时间超过这个时间终止进程
     * @return unknown
     */
    public function manualPull($strKeyOrQueue, $intTasksNum = 30, $intLifeTime = 40, $intSleepTime = 0)
    {
        $intEndTime = time() + $intLifeTime;
        $arrExchangeConf = $this->searchQueue($strKeyOrQueue);

        $this->Queue->setName($arrExchangeConf['queue']);
        $this->Queue->setFlags(AMQP_DURABLE);
        $this->Queue->declareQueue();

        while ($intTasksNum > 0) {
            $intTasksNum--;
            $Envelope = $this->Queue->get(AMQP_AUTOACK);
            if (empty($Envelope)) {
                sleep(3);
                continue;
            }
            $strMessge = $Envelope->getBody();
            if (empty($strMessge)) continue;
            $arrMessage = $arrSourceMessage = json_decode($strMessge, true);

            $ReflectionMethod = new \ReflectionMethod($arrMessage['callback'][0], $arrMessage['callback'][1]);
            if ($ReflectionMethod->isStatic() == false) {
                $arrMessage['callback'][0] = new $arrMessage['callback'][0];
            }

            try {
                call_user_func_array($arrMessage['callback'], [$arrMessage['param']]);
            } catch (\Throwable $t) {
                $this->errorExcuteQueue($arrSourceMessage, $t);
            }

            if ($intSleepTime > 0) {
                sleep($intSleepTime);
            }

            if ($intEndTime < time()) {
                return $this->disconnect();
            }
        }
        return $this->disconnect();
    }

    private function disconnect()
    {
        $this->AMQP->disconnect();
    }

    /**
     * @info 异常队列处理
     * @author QuickFK
     * @date 2018年3月19日 14:23:57
     */
    public function errorExcuteQueue($arrMessage, $t)
    {
        throw $t;
    }

    /**
     * @info 根据键值查询队列相关信息
     * @param string $strKeyOrQueue
     * @throws \Exception
     * @return unknown[]
     */
    private function searchQueue($strKeyOrQueue = '')
    {
        foreach ($this->arrConfig['exchange'] as $strExchange => $arrQueue) {
            foreach ($arrQueue as $strKey => $strQueue) {
                if ($strKey == $strKeyOrQueue || $strQueue == $strKeyOrQueue) {
                    return [
                        'exchange' => $strExchange,
                        'key' => $strKey,
                        'queue' => $strQueue,
                    ];
                }
            }
        }
        throw new \Exception(self::$arrCodeData['1002002']['msg'], self::$arrCodeData['1002002']['code']);
    }
}

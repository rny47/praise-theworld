<?php

namespace App\Task\Core;

class Service
{
    private $arrConfig;

    private static $obj;

    private $Server;

    static function instance($arrConfig)
    {
        if (self::$obj == null) {
            self::$obj = new self($arrConfig);
        }
        return self::$obj;
    }

    public function __construct($arrConfig)
    {
        $this->arrConfig = $arrConfig;
        $this->createServer();
    }

    private function serHttpConfig()
    {
        $this->Server->set($this->filterConfig());
        $this->Server->on('request', [Rout::class, 'httpDispense']);
    }

    private function filterConfig()
    {
        $arrConfig = $this->arrConfig;
        unset($arrConfig['host']);
        unset($arrConfig['port']);
        unset($arrConfig['mode']);
        unset($arrConfig['sockType']);
        unset($arrConfig['server_type']);
        return $arrConfig;
    }

    private function serTcpConfig()
    {
        $this->Server->set($this->filterConfig());
        $this->Server->on('receive', [Rout::class, 'tcpDispense']);
    }

    private function createServer()
    {
        switch ($this->arrConfig['server_type']) {
            case 'HTTP':
                $this->Server = new \Swoole\Http\Server($this->arrConfig['host'], $this->arrConfig['port']);
                $this->serHttpConfig();
                break;
            default:
                $this->Server = new \Swoole\Server($this->arrConfig['host'], $this->arrConfig['port'], $this->arrConfig['mode'], $this->arrConfig['sockType']);
                $this->serTcpConfig();
                break;
        }

        $this->Server->on('task', [$this, 'onTask']);

        $this->Server->on('finish', [$this, 'onFinish']);
    }

    public function getServer()
    {
        return $this->Server;
    }

    public function onTask($Http, $task_id, $from_id, $arrData)
    {
        $mixedResult = call_user_func_array($arrData[0], $arrData[1]);

        $Http->finish($mixedResult);
    }

    public function onFinish($Http, $task_id, $mixedData)
    {
    }
}

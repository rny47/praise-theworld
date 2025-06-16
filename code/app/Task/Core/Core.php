<?php

namespace App\Task\Core;

use Symfony\Component\Console\Helper\Table;
use App\Console\Commands\CustomTask;
use Illuminate\Foundation\Application;

class Core
{
    /**
     * HTTP 服务
     */
    static $HttpService;

    /**
     * 配置
     */
    static $arrDynamicConfig = [];

    /**
     * Cli 类
     *
     * @var CustomTask
     */
    protected $CustomTask = NULL;

    /**
     * CLI参数
     *
     * @var array
     */
    protected $arrArgvs = [];

    /**
     * 方法
     *
     * @var string
     */
    protected $strAction = '';

    public function __construct($arrArgvs = [], CustomTask $CustomTask = NULL)
    {
        $this->setConfigDaemonize($arrArgvs[0]['d']);
        $this->CustomTask = $CustomTask;
        $this->strAction = $arrArgvs[0]['action'];
    }

    /**
     * 设置是否后台启动
     *
     * @param string $strMode
     * @return void
     */
    private function setConfigDaemonize($strMode)
    {
        self::$arrDynamicConfig['daemonize'] = (bool)$strMode;
    }

    /**
     * 获取进程ID
     *
     * @return void
     */
    private function getPid()
    {
        return file_exists(config('task.service.pid_file')) ? file_get_contents(config('task.service.pid_file')) : NULL;
    }

    /**
     * 启动服务
     *
     * @return void
     */
    public function start()
    {
        $intPid = (int)$this->getPid();

        $this->CustomTask->getOutput()->writeln('<fg=green>正在启动....</>');

        if ($intPid > 0 && \Swoole\Process::kill($intPid, SIG_DFL)) {
            $this->info();
        } else {
            $this->createService();

            $this->addProcessList();

            $this->addScheduledTasks();

            $this->info();
            self::$HttpService->getServer()->start();
        }
    }

    /**
     * 关闭服务
     *
     * @return void
     */
    public function stop()
    {
        $intPid = (int)$this->getPid();

        $this->CustomTask->getOutput()->writeln('<fg=red>正在停止....</>');

        if ($intPid <= 0) {
        } else if (posix_kill($intPid, 0)) {
            \Swoole\Process::kill($intPid, SIGTERM);

            $intLimit = 10;
            while ($intLimit-- > 0) {
                if (posix_kill($intPid, 0)) {
                    sleep(1);
                } else {
                    break;
                }
            }
        }
        $this->info();
    }

    /**
     * 获取状态
     *
     * @return void
     */
    public function status()
    {
        $this->CustomTask->getOutput()->writeln('<fg=green>正在获取状态....</>');

        $this->info();
    }

    /**
     * 获取服务状态
     *
     * @return bool
     */
    private function getServiceStatus()
    {
        $intPid = (int)$this->getPid();

        if ($intPid <= 0) {
            return false;
        } else if (posix_kill($intPid, 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 重启服务
     *
     * @return void
     */
    public function restart()
    {
        $intPid = (int)$this->getPid();

        if ($intPid <= 0) {
            $this->start();
        } else {

            if (posix_kill($intPid, 0)) {
                $this->CustomTask->getOutput()->writeln('<fg=green>正在重启....</>');
                \Swoole\Process::kill($intPid, SIGUSR1);
            } else {
                $this->start();
            }
        }
        $this->info();
    }

    /**
     * 输出服务详情
     *
     * @return void
     */
    private function info()
    {
        $strPHPVersion = PHP_VERSION;
        $strLaravelVersion = Application::VERSION;
        $strSwooleVersion = swoole_version();

        if (in_array($this->strAction, ['start', 'restart'])) {
            $boolStatus = true;
        } else {
            $boolStatus = $this->getServiceStatus();
        }

        $strServiceStatus = ['<fg=red>停止</>', '<fg=green>运行</>'][$boolStatus];
        $intProcessNum = env('TaskNum', 4);
        $intCacheTaskNum = 0;

        $Table = new Table($this->CustomTask->getOutput());
        $Table
            ->setHeaders(['PHP', 'Laravel', 'Swoole', '服务状态', '工作进程', '待处理任务', '电报通知消息'])
            ->setRows([
                [
                    $strPHPVersion,
                    $strLaravelVersion,
                    $strSwooleVersion,
                    $strServiceStatus,
                    $intProcessNum,
                    $intCacheTaskNum,
                    0,
                ],
            ])
            ->render();
    }

    /**
     * 添加计划任务
     *
     * @return void
     */
    private function addScheduledTasks()
    {
        if (config('task.scheduled_tasks_pool')) {
            foreach (config('task.scheduled_tasks_pool') as $arrProcessTask) {
                if ($arrProcessTask['status']) {
                    $Process = new \Swoole\Process(function () use ($arrProcessTask) {
                        call_user_func([ScheduledTasks::class, 'hander'], $arrProcessTask);
                    });
                    self::$HttpService->getServer()->addProcess($Process);
                }
            }
        }
    }

    /**
     * 添加Process进程
     *
     * @return void
     */
    private function addProcessList()
    {
        if (config('task.process_pool')) {
            foreach (config('task.process_pool') as $v) {
                for ($i = 0; $i < $v['Num']; $i++) {
                    $Process = new \Swoole\Process($v['callback']);

                    self::$HttpService->getServer()->addProcess($Process);
                }
            }
        }
    }

    /**
     * 创建服务
     *
     * @return void
     */
    private function createService()
    {
        $arrHttpServiceConfig = array_merge(config('task.service'), self::$arrDynamicConfig);

        self::$HttpService = Service::instance($arrHttpServiceConfig);
    }
}

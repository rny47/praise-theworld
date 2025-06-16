<?php
// +----------------------------------------------------------------------
// | Swoole设置
// +----------------------------------------------------------------------

use App\Task\Logic\ApkPackageLogic;
use App\Task\Logic\StorageLogic;
use App\Task\Module\PlanTask;
use App\Task\Module\Robot;
use App\Task\Module\SystemCheck;
use App\Task\Module\TaskListen;

return [
    'telegram_host' => env('TELEGRAM_HOST', 'https://api.telegram.org'),

    'queue_key' => env('TASK_QUEUE_KEY', 'QueueKey'),

    'plan_task' => [
        'anti_virus_package' => [ApkPackageLogic::class, 'repackageApkByPlanTask'],
        'telegram_send_msg' => [Robot::class, 'sendMsgToTelegram'],
        'system_health_check' => [SystemCheck::class, 'checkSystemHealth'],
        'file_clean' => [StorageLogic::class, 'clearFile'],
    ],

    # Server Config
    'service' => [
        /***** 监听SOCK文件 *****/

        'host' => sys_get_temp_dir() . '/task.sock',
        'port' => 0,
        'mode' => SWOOLE_BASE,
        'sockType' => SWOOLE_UNIX_STREAM,
        'server_type' => 'TCP',

        'worker_num' => 1,
        'log_file' => storage_path() . '/logs/task.log',
        'pid_file' => storage_path() . '/task.pid',
        'task_worker_num' => 0,

        //'log_level'=>SWOOLE_LOG_NONE,
    ],

    # Process Pool, NUM 为设置的任务组进程数 , 根据自身服务器情况增减
    'process_pool' => [
        # 内部循环任务   队列消费者
        [
            'Name' => 'Inner Consume',
            'Num' => env('TaskNum', 4),
            'callback' => [TaskListen::class, 'innerConsume'],
        ],
    ],

    # 计划任务
    'scheduled_tasks_pool' => [
        [
            # 扫描数据表计划任务
            'status' => true,
            'name' => 'Plan Task ',
            'execution_interval' =>  1,
            'callback' => [PlanTask::class, 'scanPlanTask'],
            'param' => [],
        ],
    ],
];

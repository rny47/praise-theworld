<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return bool
     */
    public function run()
    {
        # 计划任务配置 （该配置采用先删除，再创建方案）
        $this->initPlanTask();

        # 初始化配置数据
        $this->initAdminNode();
        $this->initAdminConfig();
    }

    private function initAdminNode()
    {
        DB::table('admin_node')->insertOrIgnore(
            [
                [
                    'an_id'  => 36,
                    'an_pid' => 19,
                    'an_name' => '获取所有下载链接',
                    'an_code' => 'AppController@getApkDownLink',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ],
            ]
        );
    }

    private function initAdminConfig()
    {
        DB::table('admin_config')->insertOrIgnore(
            [
                [
                    'ac_id' => 1,
                    'ac_code' => 'python_path',
                    'ac_val' => 'python3',
                    'ac_description' => 'python程序位置',
                ],
                [
                    'ac_id' => 2,
                    'ac_code' => 'telegram_token',
                    'ac_val' => '6698981062:AAGvMFv4974Y2OZ7PQV7uvoZwwocxtRF63o',
                    'ac_description' => '电报密钥',
                ],
                [
                    'ac_id' => 3,
                    'ac_code' => 'telegram_chat_id',
                    'ac_val' => '-4086506452',
                    'ac_description' => '电报群ID',
                ],
            ]
        );
    }

    /**
     * 计划任务配置数据
     *
     * @return void
     */
    private function initPlanTask()
    {
        DB::table('plan_task')->insertOrIgnore(
            [
                [
                    'pt_id'  => 1,
                    'pt_name' => 'APP打防报毒包',
                    'pt_code' => 'anti_virus_package',
                    'pt_enable' => 1,
                    'pt_limit' => 5,
                    'pt_last_exec' => 0,
                ],
                [
                    'pt_id'  => 2,
                    'pt_name' => '电报消息发送',
                    'pt_code' => 'telegram_send_msg',
                    'pt_enable' => 1,
                    'pt_limit' => 5,
                    'pt_last_exec' => 0,
                ],
                [
                    'pt_id'  => 3,
                    'pt_name' => '健康检测',
                    'pt_code' => 'system_health_check',
                    'pt_enable' => 1,
                    'pt_limit' => 3600,
                    'pt_last_exec' => 0,
                ],
                [
                    'pt_id'  => 4,
                    'pt_name' => '垃圾清理',
                    'pt_code' => 'file_clean',
                    'pt_enable' => 1,
                    'pt_limit' => 60,
                    'pt_last_exec' => 0,
                ],
            ]
        );
        return true;
    }
}

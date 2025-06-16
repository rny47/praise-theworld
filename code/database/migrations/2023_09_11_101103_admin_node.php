<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AdminNodeModel;

class AdminNode extends Migration
{
    /**
     * 添加权限节点
     *
     * @return void
     */
    public function up()
    {

        AdminNodeModel::insert(
            [
                [
                    'an_id'  => 1,
                    'an_pid' => 0,
                    'an_name' => '系统管理',
                    'an_code' => '',
                    'an_is_dir' => 1,
                    'an_is_hidden' => 0,
                ]
            ]
        );

        AdminNodeModel::insert(
            [
                [
                    'an_id'  => 6,
                    'an_pid' => 1,
                    'an_name' => '系统用户列表',
                    'an_code' => 'SystemController@getAdminUserList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 7,
                    'an_pid' => 1,
                    'an_name' => '设置系统用户',
                    'an_code' => 'SystemController@saveAdminUser',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 8,
                    'an_pid' => 1,
                    'an_name' => '删除系统用户',
                    'an_code' => 'SystemController@delAdminUser',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 9,
                    'an_pid' => 1,
                    'an_name' => '角色列表',
                    'an_code' => 'SystemController@getAdminRoleList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 10,
                    'an_pid' => 1,
                    'an_name' => '设置角色',
                    'an_code' => 'SystemController@saveAdminRole',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 11,
                    'an_pid' => 1,
                    'an_name' => '删除角色',
                    'an_code' => 'SystemController@delAdminRole',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 12,
                    'an_pid' => 1,
                    'an_name' => '节点列表',
                    'an_code' => 'SystemController@getAdminNodeList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 13,
                    'an_pid' => 1,
                    'an_name' => '设置节点',
                    'an_code' => 'SystemController@saveAdminNode',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 14,
                    'an_pid' => 1,
                    'an_name' => '系统配置列表',
                    'an_code' => 'SystemController@getAdminConfigList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 15,
                    'an_pid' => 1,
                    'an_name' => '设置系统配置',
                    'an_code' => 'SystemController@saveAdminConfig',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ],
                [
                    'an_id'  => 16,
                    'an_pid' => 1,
                    'an_name' => '计划任务列表',
                    'an_code' => 'SystemController@getPlanTaskList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 17,
                    'an_pid' => 1,
                    'an_name' => '设置计划任务',
                    'an_code' => 'SystemController@savePlanTask',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 18,
                    'an_pid' => 1,
                    'an_name' => '获取异步消息',
                    'an_code' => 'SystemController@getTaskMsg',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ],

                #############  应用管理
                [
                    'an_id'  => 19,
                    'an_pid' => 0,
                    'an_name' => '应用管理',
                    'an_code' => '',
                    'an_is_dir' => 1,
                    'an_is_hidden' => 0,
                ],

                [
                    'an_id'  => 20,
                    'an_pid' => 19,
                    'an_name' => 'APK列表',
                    'an_code' => 'AppController@getApkList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 21,
                    'an_pid' => 19,
                    'an_name' => '设置APK',
                    'an_code' => 'AppController@saveApk',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 22,
                    'an_pid' => 19,
                    'an_name' => '上传APK',
                    'an_code' => 'AppController@uploadApk',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ],
                [
                    'an_id'  => 23,
                    'an_pid' => 19,
                    'an_name' => '打包APK',
                    'an_code' => 'AppController@repackageApk',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ],



                #############  存储管理
                [
                    'an_id'  => 24,
                    'an_pid' => 0,
                    'an_name' => '存储管理',
                    'an_code' => '',
                    'an_is_dir' => 1,
                    'an_is_hidden' => 0,
                ],

                [
                    'an_id'  => 25,
                    'an_pid' => 24,
                    'an_name' => 'OSS列表',
                    'an_code' => 'AppController@getOssList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 26,
                    'an_pid' => 24,
                    'an_name' => '设置OSS',
                    'an_code' => 'AppController@saveOss',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 27,
                    'an_pid' => 24,
                    'an_name' => '存储桶列表',
                    'an_code' => 'AppController@getBucketList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 28,
                    'an_pid' => 24,
                    'an_name' => '存储桶文件列表',
                    'an_code' => 'AppController@getBucketFileList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ],

                #############  报表
                [
                    'an_id'  => 29,
                    'an_pid' => 0,
                    'an_name' => '报表管理',
                    'an_code' => '',
                    'an_is_dir' => 1,
                    'an_is_hidden' => 0,
                ],
                [
                    'an_id'  => 30,
                    'an_pid' => 29,
                    'an_name' => '报表列表',
                    'an_code' => 'AppController@getReportList',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ],
            ]
        );
    }

    /**
     * 回滚
     *
     * @return void
     */
    public function down()
    {
        AdminNodeModel::whereIn('an_id', [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26
        ])->delete();
    }
}

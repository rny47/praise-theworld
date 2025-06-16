<?php

use App\Models\AdminNodeModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDelDataNode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AdminNodeModel::insert(
            [
                [
                    'an_id'  => 31,
                    'an_pid' => 19,
                    'an_name' => '删除应用',
                    'an_code' => 'AppController@delApk',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 32,
                    'an_pid' => 24,
                    'an_name' => '删除OSS',
                    'an_code' => 'AppController@delOss',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 33,
                    'an_pid' => 24,
                    'an_name' => '删除存储桶',
                    'an_code' => 'AppController@delBucket',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 1,
                ], [
                    'an_id'  => 34,
                    'an_pid' => 24,
                    'an_name' => '删除文件',
                    'an_code' => 'AppController@delBucketFile',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ], [
                    'an_id'  => 35,
                    'an_pid' => 1,
                    'an_name' => '获取当前用户权限节点ID',
                    'an_code' => 'SystemController@getCurrentAdminUserRoleNode',
                    'an_is_dir' => 0,
                    'an_is_hidden' => 0,
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oss', function (Blueprint $Table) {
            $Table->id("o_id")->comment("id");
            $Table->string('o_name', 1000)->nullable(false)->default('')->comment('OSS名字');
            $Table->tinyInteger('o_status', false, true)->nullable(false)->default(0)->comment('状态：0=正常,1=删除,2=过度');
            $Table->tinyInteger('o_quicken', false, true)->nullable(false)->default(0)->comment('开启加速：0=不开启,1=开启');
            $Table->string('o_type', 1000)->nullable(false)->default('')->comment('类型：tencent=腾讯');
            $Table->string('o_key_id', 1000)->nullable(false)->default('')->comment('KeyId');
            $Table->string('o_key_secret', 1000)->nullable(false)->default('')->comment('KeySecret');
            $Table->string('o_app_id', 1000)->nullable(false)->default('')->comment('桶应用ID');
            $Table->string('o_region', 1000)->nullable(false)->default('')->comment('区域');

            $Table->timestamp("created_at", 0)->useCurrent()->comment("添加时间");
            $Table->timestamp("updated_at", 0)->nullable()->useCurrentOnUpdate()->comment("删除时间");
            $Table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oss');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apk', function (Blueprint $Table) {
            $Table->id("a_id")->comment("id");
            $Table->tinyInteger('a_status', false, true)->nullable(false)->default(0)->comment('状态：0=正常,1=删除');

            $Table->string('a_name', 255)->nullable(false)->default('')->comment('应用名称');
            $Table->string('a_package_name', 1000)->nullable(false)->default('')->comment('APK包名');
            $Table->string('a_save_path', 1000)->nullable(false)->default('')->comment('APK源包地址');

            $Table->string('a_down_uri', 1000)->nullable(false)->default('')->comment('APK下载链接后缀');

            $Table->integer('o_id', false, true)->nullable(false)->default(0)->comment('OSS ID');

            $Table->json('a_domain')->nullable(true)->comment('301域名');

            $Table->tinyInteger('a_enable', false, true)->nullable(false)->default(0)->comment('自动打包开关');
            $Table->integer('a_limit', false, true)->nullable(false)->default(0)->comment('打包频率');

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
        Schema::dropIfExists('apk');
    }
}

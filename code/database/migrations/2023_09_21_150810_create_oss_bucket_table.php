<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOssBucketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oss_bucket', function (Blueprint $Table) {
            $Table->id("ob_id")->comment("id");

            $Table->tinyInteger('ob_status', false, true)->nullable(false)->default(0)->comment('状态：0=正常,1=删除,2=过度');

            $Table->integer('a_id', false, true)->nullable(false)->default(0)->comment('APK ID');

            $Table->integer('o_id', false, true)->nullable(false)->default(0)->comment('OSS ID');

            $Table->integer('ob_sort', false, true)->nullable(false)->default(0)->comment('优先级');

            $Table->string('ob_name', 255)->nullable(false)->default('')->comment('名字');

            $Table->string('ob_location', 255)->nullable(false)->default('')->comment('地区');

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
        Schema::dropIfExists('oss_bucket');
    }
}

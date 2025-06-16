<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report', function (Blueprint $Table) {
            $Table->id("r_id")->comment("id");

            $Table->integer('a_id', false, true)->nullable(false)->default(0)->comment('APK ID');
            $Table->integer('r_date', false, true)->nullable(false)->default(0)->comment('时间');
            $Table->integer('r_num', false, true)->nullable(false)->default(0)->comment('计数');

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
        Schema::dropIfExists('report');
    }
}

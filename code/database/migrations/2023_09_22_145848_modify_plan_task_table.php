<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPlanTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_task', function (Blueprint $Table) {
            $Table->integer('pt_last_exec')->nullable(false)->default(0)->after('pt_limit')->comment('最后执行时间时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_task', function (Blueprint $Table) {
            $Table->dropColumn('pt_last_exec');
        });
    }
}

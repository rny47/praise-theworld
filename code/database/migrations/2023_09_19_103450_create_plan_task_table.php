<?php

use App\Models\PlanTaskModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_task', function (Blueprint $Table) {
            $Table->id("pt_id")->comment("id");
            $Table->string('pt_name', 255)->nullable(false)->default('')->comment('任务名');
            $Table->string('pt_code', 255)->nullable(false)->default('')->comment('任务编码');
            $Table->tinyInteger('pt_enable', false, true)->nullable(false)->default(0)->comment('任务开关');
            $Table->integer('pt_limit', false, true)->nullable(false)->default(0)->comment('任务频率');

            $Table->timestamp("created_at", 0)->useCurrent()->comment("添加时间");
            $Table->timestamp("updated_at", 0)->nullable()->useCurrentOnUpdate()->comment("删除时间");
            $Table->softDeletes('deleted_at', 0);

            $Table->unique('pt_code');
            $Table->index('pt_enable');
        });

        PlanTaskModel::create([
            'pt_id' => 1,
            'pt_name' => 'APP打防报毒包',
            'pt_code' => 'anti_virus_package',
            'pt_enable' => 1,
            'pt_limit' => 10000000,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_task');
    }
}

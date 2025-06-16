<?php


namespace App\Task\Module;

use App\Models\PlanTaskModel;
use App\Task\Logic\SystemLogic;
use TaskManager\TaskCore;

class PlanTask
{
    /**
     * 读取所有计划任务, 并且启动计划任务
     * @return bool
     */
    public static function scanPlanTask()
    {
        SystemLogic::disconnect();

        $PlanTaskAll = PlanTaskModel::where('pt_enable', 1)->get();

        $TaskCore = new TaskCore();

        foreach ($PlanTaskAll as $PlanTask) {
            if (
                $PlanTask->getRawOriginal('pt_last_exec')  != 0
                && ($PlanTask->getRawOriginal('pt_last_exec') + $PlanTask->getRawOriginal('pt_limit')) > time()
            ) continue;

            $PlanTask->pt_last_exec = time();
            $PlanTask->save();

            $arrCallBack = config('task.plan_task.' . $PlanTask->pt_code);
            if ($arrCallBack) {
                $arrTask = [
                    'callback' =>  $arrCallBack,
                    'data' => []
                ];
                $TaskCore->set($arrTask);
            }
        }
        return true;
    }
}

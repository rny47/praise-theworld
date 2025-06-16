<?php

namespace App\Task\Logic;

use App\Models\ReportModel;

class ReportLogic
{
    /**
     * 下载数自增
     *
     * @param array $arrData
     * @return void
     */
    public function addDownNum($arrData)
    {
        $intDate = date('Ymd');
        $ReportModel = ReportModel::where('a_id', $arrData['a_id'])
            ->where('r_date', $intDate)
            ->first();

        if (empty($ReportModel)) {
            $ReportModel = new ReportModel;
            $ReportModel->a_id = $arrData['a_id'];
            $ReportModel->r_date = $intDate;
            $ReportModel->r_num = 0;
            $ReportModel->save();
        }

        $ReportModel->increment('r_num');
        $ReportModel->save();
    }
}

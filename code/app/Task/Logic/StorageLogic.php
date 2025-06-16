<?php

namespace App\Task\Logic;

use App\Models\FileCleanModel;
use App\Models\OssBucketFileModel;
use App\Models\OssBucketModel;
use App\Models\OssModel;
use GuzzleHttp\Client;


class StorageLogic
{

    /**
     * 下载文件，建立缓存
     *
     * @param array $arrData
     * @return bool
     */
    public function makeCacheByOssBucketFile(array $arrData)
    {
        try {
            $OssBucketFileModel = OssBucketFileModel::whereObfId($arrData['obf_id'])->firstOrFail();
            $strUrl = $OssBucketFileModel->getDownUrl(false);

            $Client = new Client(['verify' => false]);

            $Response = $Client->get($strUrl, [
                'timeout' => 180,
                'connect_timeout' => 180,
            ]);

            return $Response->getStatusCode() == 200;
        } catch (\Throwable $t) {
            $strMsg =  sprintf('[%s],预下载链接[%s]未能成功下载，错误原因[%s]。' . PHP_EOL, date('Y-m-d H:i:s'), $strUrl ?? '', $t->getMessage());
            echo $strMsg;
        }
    }


    /**
     * 清理文件和存储桶
     *
     * @param array $arrData
     * @return void
     */
    public function clearOssFile($arrData)
    {
        $OssBucketFileModelAll = OssBucketFileModel::where('a_id', $arrData['a_id'])
            ->where('obf_status', 0)
            ->orderBy('obf_sort', 'desc')
            ->get();

        # 最新在用的包移出来
        $NewOssBucketFileModel =  $OssBucketFileModelAll->shift();

        foreach ($OssBucketFileModelAll as $OldOssBucketFileModel) {
            $OssBucketModel = OssBucketModel::from('oss_bucket as ob')
                ->where('ob.ob_id', $OldOssBucketFileModel->ob_id)
                ->leftjoin('oss as o', 'o.o_id', 'ob.o_id')
                ->select(['o.o_key_id', 'o.o_key_secret', 'o.o_region', 'ob.*'])
                ->first();

            $OssModel = OssModel::whereOId($OssBucketModel->o_id)->first();

            $CosCore = $OssModel->getCosCore();

            $strObName = $OssBucketModel->ob_name;

            $CosCore->delFile($strObName, $OldOssBucketFileModel->obf_key);

            $OldOssBucketFileModel->delete();

            # 存储桶没有文件了，则删除
            $arrFileList = $CosCore->getBucketFileList($strObName)->toArray();

            if (!isset($arrFileList['Contents']) || empty($arrFileList['Contents'])) {
                $CosCore->delBucket($strObName);
                OssBucketModel::where('ob_id', $OssBucketModel->ob_id)->delete();
            }
        }
    }

    /**
     * 清理文件和存储桶
     *
     * @param array $arrData
     * @return void
     */
    public function clearFile($arrData)
    {
        $FileCleanModelAll = FileCleanModel::where('fc_deltime', '<', time())
            ->get();

        $strCmdFomart = 'rm -rf %s';

        foreach ($FileCleanModelAll as $FileCleanModel) {
            $strCmd = sprintf($strCmdFomart, $FileCleanModel->fc_file);
            shell_exec($strCmd);
            $FileCleanModel->delete();
        }
    }
}

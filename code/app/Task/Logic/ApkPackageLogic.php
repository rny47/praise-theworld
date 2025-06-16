<?php

namespace App\Task\Logic;

use App\Models\AdminConfigModel;
use App\Models\ApkModel;
use App\Models\OssBucketFileModel;
use App\Models\OssBucketModel;
use App\Models\OssModel;
use TaskManager\MessageCore;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use TaskManager\TaskCore;
use Illuminate\Support\Facades\Redis;
use TencentCore\TencentCosCore;
use Amazon\AmazonOssCore;
use App\Models\FileCleanModel;
use Cache;
use TaskManager\RobotCore;

class ApkPackageLogic
{
    /**
     * 消息对象
     *
     * @var MessageCore
     */
    private $MessageCore = NULL;

    /**
     * 机器人对象
     *
     * @var RobotCore
     */
    private $RobotCore = NULL;

    /**
     * APK 模型对象
     *
     * @var ApkModel
     */
    private $ApkModel = NULL;

    /**
     * OSS 模型对象
     *
     * @var OssModel
     */
    private $OssModel = NULL;

    /**
     * CosCore 对象
     *
     * @var TencentCosCore|AmazonOssCore
     */
    private $CosCore = NULL;

    /**
     * OssBucket  模型对象
     *
     * @var OssBucketModel
     */
    private $OssBucketModel = NULL;

    /**
     * OssBucketFileModel  模型对象
     *
     * @var OssBucketFileModel
     */
    private $OssBucketFileModel = NULL;

    /**
     * 源包绝对路径
     *
     * @var string
     */
    private $strSourceApkPath = '';

    /**
     * 新包绝对路径
     *
     * @var string
     */
    private $strNewApkPath = '';

    /**
     * Apk模型ID
     *
     * @var integer
     */
    private $intAId = 0;

    /**
     * 是否强制更换bucket
     *
     * @var boolean
     */
    private $boolForceReplaceBucket = false;

    /**
     * 老的文件OSS模型
     *
     * @var OssBucketFileModel
     */
    private $OldOssBucketFileModel = null;

    /**
     * 日志相对路径
     *
     * @var string
     */
    private $strLogFileName;

    /**
     * 日志绝对路径
     *
     * @var string
     */
    private $strLogFilePath;
    /**
     * 最后执行指令
     *
     * @var string
     */
    private $strLastCmd = '';

    /**
     * 检测锁
     *
     * @return bool
     */
    private function checkLock()
    {
        $strKey = 'Lock:RepackageApk:' . $this->intAId;

        $intNum = Redis::connection('cache')->incr($strKey);
        Redis::connection('cache')->expire($strKey, 10 * 3600);
        if ($intNum > 1) {
            return false;
        }
        return true;
    }

    /**
     * 解锁
     *
     * @return bool
     */
    private function delLock()
    {
        $strKey = 'Lock:RepackageApk:' . $this->intAId;
        Redis::connection('cache')->del($strKey);
        return true;
    }

    /**
     * 进度状态
     *
     * @var integer
     */
    private $intRepackageStatus = 0;

    /**
     * 打包
     *
     * @param array $arrData
     * @return void
     */
    public function repackageApk($arrData)
    {
        try {
            $this->MessageCore = new MessageCore($arrData['msg_id']);

            $this->RobotCore = new RobotCore();

            $this->intAId = $arrData['a_id'];
            if ($this->checkLock() == false) {
                $strMsg = sprintf(date('Y-m-d H:i:s') . " : 已存在相同任务处理中，无法创建新的任务！[%s]", $this->intAId);
                $this->MessageCore->sendErrorMsg($strMsg);
                return false;
            };

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 任务开始！");
            $this->MessageCore->sendStartMsg($strMsg);

            $this->initApkModel();

            $this->boolForceReplaceBucket = filter_var($arrData['replace_bucket'], FILTER_VALIDATE_BOOLEAN);

            if ($this->checkErrorForceReplateBucket()) {
                $this->boolForceReplaceBucket = true;
            }

            $this->initOssModel();

            $this->pythonZip();

            $this->intRepackageStatus++;

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 本地APK打包成功！开始初始化公共云存储桶！");
            $this->MessageCore->sendMsg($strMsg);

            $this->initCosCore();

            $this->initOssBucketModel();

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 存储桶初始化成功！当前使用的公共云产品为：" . $this->OssModel->o_type_name);
            $this->MessageCore->sendMsg($strMsg);

            $this->checkBucketAccelerate();

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 开始初始化存储文件对象！");
            $this->MessageCore->sendMsg($strMsg);

            $this->initOssBucketFileModel();

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 初始化存储文件对象成功！开始推送APK至公共COS！");
            $this->MessageCore->sendMsg($strMsg);
            $this->uploadFileToOss();

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 推送APK至公共COS成功！开始处理后事！");
            $this->MessageCore->sendMsg($strMsg);

            $this->finishTask();

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 防报毒APK打包任务结束！");
            $this->MessageCore->sendEndMsg($strMsg);

            $this->delLock();
        } catch (\Throwable $t) {
            # 解锁
            $this->delLock();

            # 发送异常消息
            $this->sendErrorMsg($t);

            # 任务异常结束请求
            $this->finishErrorTask();

            # 打印异常
            debugErr($t, '打包异常！', $arrData);
        }
    }

    /**
     * 增加异常次数
     *
     * @return void
     */
    private function addErrorNum()
    {
        if ($this->intRepackageStatus > 0) {
            $strErrCacheKey = 'ApkSigner:Error:' . $this->ApkModel->a_id;
            Redis::connection('cache')->incr($strErrCacheKey);
            Redis::connection('cache')->expire($strErrCacheKey, 20 * 60);
        }
    }

    /**
     * 删除异常计数
     *
     * @return void
     */
    private function delErrorNum()
    {
        $strErrCacheKey = 'ApkSigner:Error:' . $this->ApkModel->a_id;
        Redis::connection('cache')->delete($strErrCacheKey);
    }

    /**
     * 检测连续异常次数是否达到阈值
     *
     * @return bool
     */
    private function checkErrorForceReplateBucket()
    {
        $strErrCacheKey = 'ApkSigner:Error:' . $this->ApkModel->a_id;
        return Redis::connection('cache')->get($strErrCacheKey) >= 2;
    }

    /**
     * 发送异常消息
     *
     * @param \Throwable $t
     * @return void
     */
    private function sendErrorMsg(\Throwable $t)
    {

        # 发送电报异常消息
        $strAppName = config('app.name');
        if ($this->strTelegramErrorMsg != '') {
            $strMsg = $this->strTelegramErrorMsg;
        } else {
            $strMsg = '应用：[%s]' . PHP_EOL .
                '存储桶：[%s]' . PHP_EOL .
                '打包失败原因: [%s]';
            $strMsg = sprintf(
                $strMsg,
                $this->ApkModel->a_name ?? '',
                $this->OssBucketModel->ob_name ?? '',
                $t->getMessage()
            );
        }
        $strRobotMsg = sprintf('%s %s 平台：%s', $strMsg, PHP_EOL, $strAppName);
        $this->RobotCore->sendErrorMsg($strRobotMsg);

        # 发送任务处理进度消息
        $strMsg = sprintf(date('Y-m-d H:i:s') . " : 防报毒APK打包失败,原因：[%s]！", $strMsg);
        $this->MessageCore->sendErrorMsg($strMsg);
    }

    /**
     *  任务失败的结束时做的一些工作
     *
     * @return void
     */
    private function finishErrorTask()
    {
        if ($this->OssBucketFileModel && $this->OssBucketFileModel->obf_status == 2) {
            try {
                $this->CosCore->delFile($this->OssBucketModel->ob_name, $this->OssBucketFileModel->obf_key);
            } catch (\Exception $e) {
                debugErr($e, '删除存储文件异常！');
            }

            $this->OssBucketFileModel->delete();
        }

        if ($this->OssBucketModel && $this->OssBucketModel->ob_status == 2) {

            $OldOssBucketFileModel = OssBucketFileModel::where('ob_id', $this->OssBucketModel->ob_id)->first();
            if (empty($OldOssBucketFileModel)) {
                # 存储桶如果没有数据，则删除存储桶
                try {
                    $this->CosCore->delBucket($this->OssBucketModel->ob_name);
                    $this->OssBucketModel->delete();
                } catch (\Exception $e) {
                    debugErr($e, '删除存储桶异常！');
                }
            }
        }

        // timed out
        // User network is too slow
        $this->addErrorNum();
    }

    /**
     * 任务成功结束时做的一些工作
     *
     * @return bool
     */
    private function finishTask()
    {
        $intTime = time();
        $this->OssBucketModel->ob_sort = $intTime;
        $this->OssBucketModel->ob_status = 0;
        $this->OssBucketModel->save();

        $intTime = time();
        $this->OssBucketFileModel->obf_sort = $intTime;
        $this->OssBucketFileModel->obf_status = 0;
        $this->OssBucketFileModel->save();

        $this->ApkModel->a_last_package = $intTime;
        $this->ApkModel->save();

        # 对一个文件建立缓存
        $TaskCore = new TaskCore();
        $arrTask = [
            'callback' => [StorageLogic::class, 'makeCacheByOssBucketFile'],
            'data' => [
                'obf_id' => $this->OssBucketFileModel->obf_id,
            ]
        ];

        $TaskCore->set($arrTask);

        # 清理文件和存储桶
        $TaskCore = new TaskCore();
        $arrTask = [
            'callback' => [StorageLogic::class, 'clearOssFile'],
            'data' => [
                'a_id' => $this->intAId,
            ]
        ];

        $TaskCore->set($arrTask);

        $this->setLogDelTask(3600);
        $this->setApkDelTask();

        $this->delErrorNum();

        return true;
    }

    /**
     * 初始化APK模型
     *
     * @return bool
     */
    private function initApkModel()
    {
        $this->ApkModel = ApkModel::where('a_id', $this->intAId)->where('a_status', 0)->first();

        if (empty($this->ApkModel)) {

            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 未找到[%s]应用！", $this->intAId);

            throw new \Exception($strMsg);
        }
        return true;
    }

    /**
     * 初始化OSS模型
     *
     * @return bool
     */
    private function initOssModel()
    {
        $this->OssModel = OssModel::where('o_id', $this->ApkModel->o_id)->where('o_status', 0)->first();

        if (empty($this->OssModel)) {
            $strMsg = sprintf(date('Y-m-d H:i:s') . " : 未找到[%s]OSS！", $this->ApkModel->a_name);

            throw new \Exception($strMsg);
        }
        return true;
    }


    /**
     * FileCleanModel
     *
     * @var FileCleanModel
     */
    private $ApkDelTask = NULL;

    /**
     * 设置APK清理时间
     *
     * @param int $intLifeTime
     * @return void
     */
    private function setApkDelTask($intLifeTime = 24 * 3600)
    {
        if ($this->ApkDelTask === NULL) {
            $this->ApkDelTask = new FileCleanModel();
        }
        $this->ApkDelTask->fc_file = $this->strNewApkPath;
        $this->ApkDelTask->fc_deltime = time() + $intLifeTime;
        $this->ApkDelTask->save();
    }

    /**
     * FileCleanModel
     *
     * @var FileCleanModel
     */
    private $LogDelTask = NULL;

    /**
     * 设置APK清理时间
     *
     * @param int $intLifeTime
     * @return void
     */
    private function setLogDelTask($intLifeTime = 24 * 3600)
    {
        if ($this->LogDelTask === NULL) {
            $this->LogDelTask = new FileCleanModel();
        }
        $this->LogDelTask->fc_file = $this->strLogFilePath;
        $this->LogDelTask->fc_deltime = time() + $intLifeTime;
        $this->LogDelTask->save();
    }

    /**
     * 打防报毒包
     *
     * @return bool
     */
    private function pythonZip()
    {
        $this->strSourceApkPath = Storage::disk('apk')->path($this->ApkModel->a_save_path);

        $this->strNewApkPath = dirname($this->strSourceApkPath) . '/tmp_' . Str::random(10) . '.apk';

        $this->setApkDelTask();

        // $this->strNewApkPath = '/www/ApkSigner/code/storage/app/apk/2023/09/21/1695304093.apk';

        $strPython = AdminConfigModel::getAcValByAcCode('python_path');

        $boolAIsCompress =  $this->ApkModel->a_is_compress ? 'true' : 'false';

        $boolAReplacePackagerName =  $this->ApkModel->a_replace_packager_name ? 'true' : 'false';


        // 获取public目录的绝对路径
        $strPublicPath = public_path();

        // 生成文件名，格式为年月日.log
        $this->strLogFileName = date('Ymd') . '_' . Str::random(10) . '.log';

        // 拼接文件路径
        $this->strLogFilePath = $strPublicPath . '/' . $this->strLogFileName;

        $this->setLogDelTask();

        $strReplacePackageName = sprintf("--need-change-package-name %s ", $boolAReplacePackagerName);
        if (!empty($this->ApkModel->a_new_package_name)) {
            $strReplacePackageName .=  sprintf("--new-package-name %s ", $this->ApkModel->a_new_package_name);
        }

        $strCmd = sprintf(
            "%s %s " .
                "--original-apk-path %s " .
                "--target-apk-path %s " .
                "--need-shrink %s " .
                "%s > %s 2>&1",
            $strPython,
            base_path('ApkCleaner/bin/clean.py'),
            $this->strSourceApkPath,
            $this->strNewApkPath,
            $boolAIsCompress,
            $strReplacePackageName,
            $this->strLogFilePath
        );

        $this->strLastCmd = $strCmd;

        // echo $strCmd;
        $strMsg = sprintf(date('Y-m-d H:i:s') . " : 开始打包防报毒APK至本地！");
        $this->MessageCore->sendStartMsg($strMsg);

        $strMsg = sprintf(date('Y-m-d H:i:s') . " : " . $strCmd);
        $this->MessageCore->sendStartMsg($strMsg);

        $intResultCode = 0;
        passthru($strCmd,  $intResultCode);
        // var_dump($intResultCode);

        return $this->checkNewApk();
    }

    /**
     * 检测新报是否合法
     *
     * @return bool
     */
    private function checkNewApk()
    {
        $strCmd = "/usr/local/android-11/aapt dump badging %s | awk -F\"'\" '/package: name=/{{print $2}}'";
        $strCmd =  sprintf($strCmd, $this->strNewApkPath);

        $strPackageName = shell_exec($strCmd);

        $strPackageName = trim($strPackageName);

        if (empty($strPackageName)) {
            $strMsg = sprintf(date('Y-m-d H:i:s') . " : Python打防报毒包失败[%s]！", $this->ApkModel->a_name);

            $this->strTelegramErrorMsg = sprintf(
                "时间: %s" . PHP_EOL .
                    "应用名称: %s" . PHP_EOL .
                    "打包指令: %s" . PHP_EOL .
                    "错误: %s" . PHP_EOL .
                    "处理人: %s" . PHP_EOL,
                date('Y-m-d H:i:s'),
                $this->ApkModel->a_name,
                $this->strLastCmd,
                sprintf("请访问链接[%s]查看详情", $this->strLogFileName),
                '@spark_ph',
            );

            throw new \Exception($strMsg);
        }
        return true;
    }

    private $strTelegramErrorMsg = '';

    /**
     * 上传文件到OSS
     *
     * @return bool
     */
    private function uploadFileToOss()
    {
        $this->CosCore->uploadFile(
            $this->OssBucketModel->ob_name,
            $this->strNewApkPath,
            $this->OssBucketFileModel->obf_key
        );
        return true;
    }

    /**
     * OssBucketFileModel 初始化
     *
     * @return OssBucketFileModel
     */
    private function initOssBucketFileModel()
    {
        $this->OldOssBucketFileModel = OssBucketFileModel::where('obf_status', 0)
            ->where('a_id', $this->ApkModel->a_id)
            ->where('o_id', $this->OssModel->o_id)
            ->where('ob_id', $this->OssBucketModel->ob_id)
            ->orderBy('obf_sort', 'desc')
            ->first();
        // if (empty($this->OssBucketFileModel)) {
        //     $this->createOssBucketFile();
        // }

        $this->createOssBucketFile();

        return $this->OssBucketFileModel;
    }

    /**
     * 创建存储桶文件模型
     *
     * @return OssBucketFileModel
     */
    public function createOssBucketFile()
    {
        $this->OssBucketFileModel = new OssBucketFileModel;
        $this->OssBucketFileModel->obf_status = 2;
        $this->OssBucketFileModel->a_id = $this->ApkModel->a_id;
        $this->OssBucketFileModel->o_id = $this->OssModel->o_id;
        $this->OssBucketFileModel->ob_id = $this->OssBucketModel->ob_id;
        $this->OssBucketFileModel->obf_key = Str::random(6) . '-' . time() . '.apk';
        $this->OssBucketFileModel->save();
        return $this->OssBucketFileModel;
    }

    /**
     * 初始化公共云OSS
     *
     * @return bool
     */
    private function initCosCore($boolAccelerate = false)
    {
        $this->CosCore = $this->OssModel->getCosCore($boolAccelerate);
        return true;
    }


    /**
     * 获取OSS存储桶
     *
     * @return OssBucketModel
     */
    private function initOssBucketModel()
    {
        $this->OssBucketModel = OssBucketModel::where('ob_status', 0)
            ->where('a_id', $this->ApkModel->a_id)
            ->where('o_id', $this->OssModel->o_id)
            ->orderBy('ob_sort', 'desc')
            ->first();

        if (empty($this->OssBucketModel) || $this->boolForceReplaceBucket == true) {
            $this->createBucket();
        }

        return $this->OssBucketModel;
    }

    /**
     * 创建存储桶
     *
     * @return OssBucketModel
     */
    public function createBucket()
    {
        if ($this->OssModel->o_type == 'tencent') {
            $strBucket = strtolower(Str::random(6)) . '-' . $this->OssModel->o_app_id;
            $arrResult = $this->CosCore->createBucket($strBucket)->toArray();
            $this->CosCore->setBucketAcl($strBucket);
        } else {
            $strBucket = strtolower(Str::random(8));
            $arrResult = $this->CosCore->createBucket($strBucket)->toArray();
        }

        $this->OssBucketModel = new OssBucketModel;
        $this->OssBucketModel->ob_status = 2;
        $this->OssBucketModel->ob_name = $strBucket;
        $this->OssBucketModel->a_id = $this->ApkModel->a_id;
        $this->OssBucketModel->o_id = $this->OssModel->o_id;
        $this->OssBucketModel->ob_location = $arrResult['Location'];
        $this->OssBucketModel->save();


        return $this->OssBucketModel;
    }

    /**
     * 检测是否开启了全球加速
     *
     * @return void
     */
    public function checkBucketAccelerate()
    {
        $arrAccelerateInfo = $this->CosCore
            ->getBucketAccelerate($this->OssBucketModel->ob_name)
            ->toArray();

        if ($this->OssModel->o_quicken == 1) {
            if (
                !isset($arrAccelerateInfo['Status']) ||
                $arrAccelerateInfo['Status'] != 'Enabled'
            ) {
                $strMsg = sprintf(
                    "%s : 存储桶[%s]未开启全球加速,正在开启全球加速......",
                    date('Y-m-d H:i:s'),
                    $this->OssBucketModel->ob_name
                );
                $this->MessageCore->sendMsg($strMsg);

                $this->CosCore->setBucketAccelerate($this->OssBucketModel->ob_name, true);

                $strMsg = sprintf(
                    "%s : 存储桶[%s]开启全球加速成功。",
                    date('Y-m-d H:i:s'),
                    $this->OssBucketModel->ob_name
                );
                $this->MessageCore->sendMsg($strMsg);
            }

            $this->initCosCore(true);
        }
    }

    /**
     * 计划任务打包
     *
     * @return void
     */
    static public function repackageApkByPlanTask()
    {
        $ApkModelAll = ApkModel::where('a_status', 0)
            ->where('a_enable', 1)
            ->get();

        $TaskCore = new TaskCore();
        $intTime = time();
        foreach ($ApkModelAll as $ApkModel) {

            if (($ApkModel->getRawOriginal('a_limit') + $ApkModel->getRawOriginal('a_last_package')) > $intTime) {
                continue;
            }

            $ApkModel->a_last_package = $intTime;
            $ApkModel->save();

            $MessageCore = new MessageCore();

            $arrTask = [
                'callback' => [ApkPackageLogic::class, 'repackageApk'],
                'data' => [
                    'a_id' => $ApkModel->a_id,
                    'replace_bucket' => false,
                    'msg_id' => $MessageCore->getMsgQueueKey(),
                ]
            ];

            $TaskCore->set($arrTask);
        }
    }
}

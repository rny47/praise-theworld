<?php

namespace Modules\Admin\Http\Controllers;

use Amazon\AmazonOssCore;
use App\Models\AdminUsersModel;
use App\Models\ApkModel;
use App\Models\OssBucketFileModel;
use App\Models\OssBucketModel;
use App\Models\OssModel;
use App\Models\ReportModel;
use App\Task\Logic\ApkPackageLogic;
use Aws\S3\S3Client;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use MinOSS\MinIOCore;
use Modules\Admin\Http\Middleware\BaseController;
use TaskManager\MessageCore;
use TaskManager\TaskCore;
use TencentCore\TencentCosCore;

class AppController extends BaseController
{
    /**
     * 获取下载链接
     *
     * @param Request $Request
     * @param string $strPattern
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getDownUrl(Request $Request, $strPattern)
    {
        try {
            $strUrl = "";

            $ApkModel = ApkModel::whereADownUri($strPattern)
                ->whereAStatus(0)
                ->firstOrFail();

            $OssBucketFileModel = OssBucketFileModel::whereAId($ApkModel->a_id)
                ->whereObfStatus(0)
                ->orderBy('obf_sort', 'desc')
                ->firstOrFail();

            $strUrl = $OssBucketFileModel->getDownUrl(true);

            $ApkModel->addDownNum();

            return $this->success([
                'u' => $strUrl,
            ]);
        } catch (\Exception $e) {
            return $this->error();
        }
    }

    /**
     * 下载APK
     *
     * @param Request $Request
     * @param string $strPattern
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function downApk(Request $Request, $strPattern)
    {
        try {
            $ApkModel = ApkModel::whereADownUri($strPattern)
                ->whereAStatus(0)
                ->firstOrFail();

            $OssBucketFileModel = OssBucketFileModel::whereAId($ApkModel->a_id)
                ->whereObfStatus(0)
                ->orderBy('obf_sort', 'desc')
                ->firstOrFail();

            $strUrl = $OssBucketFileModel->getDownUrl(true);

            $ApkModel->addDownNum();

            return redirect($strUrl)->setStatusCode(302);
        } catch (\Exception $e) {
        }
        return response(null, 404);
    }

    /**
     * 上传APK
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function uploadApk(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'file' => $Request->file('file'),
        ];
        $arrRules = [
            'file' => 'required|file|max:1024000'
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $File = $Request->file('file');

        $strDir = now()->format('Y/m/d');

        $strFileName = time() . '.' . $File->getClientOriginalExtension();

        $strPath = $File->storeAs($strDir, $strFileName, 'apk');

        $strAbsolutePath = Storage::disk('apk')->path($strPath);

        $strPackageName = shell_exec("/usr/local/android-11/aapt dump badging $strAbsolutePath | awk -F\"'\" '/package: name=/{{print $2}}'");

        $strPackageName = trim($strPackageName);

        return $this->success([
            'a_save_path' => $strPath,
            'a_package_name' => $strPackageName,
        ]);
    }

    /**
     * APK列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getApkList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $ApkModel = ApkModel::from('apk as a')
            ->leftJoin('oss as o', 'o.o_id', '=', 'a.o_id')
            ->orderBy('a.a_id', 'desc')
            ->whereNull('a.deleted_at')
            ->paginate($Request->get('limit'),  [
                'a.*',
                'o.o_name'
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $ApkModel->currentPage(),
            'total_page' => $ApkModel->lastPage(),
            'limit' => $ApkModel->perPage(),
            'item' => $ApkModel->items(),
            'total' => $ApkModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 设置Apk
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function saveApk(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'a_id' => $Request->post('a_id'),
            // 'a_status' => $Request->post('a_status'),
            'a_name' => $Request->post('a_name'),
            'a_package_name' => $Request->post('a_package_name'),
            'a_save_path' => $Request->post('a_save_path'),
            'a_down_uri' => $Request->post('a_down_uri'),
            'o_id' => $Request->post('o_id'),
            'a_domain' => $Request->post('a_domain'),
            'a_enable' => $Request->post('a_enable'),
            'a_limit' => $Request->post('a_limit'),
            'a_replace_packager_name' => $Request->post('a_replace_packager_name'),
            'a_is_compress' => $Request->post('a_is_compress'),
            'a_new_package_name' => (string)$Request->post('a_new_package_name'),
        ];

        $arrRules = [
            // 'a_status' => 'required|numeric|in:0,1',
            'a_name' => 'required|string|max:255',
            'a_package_name' => 'required|string|max:1000',
            'a_save_path' => 'required|string|max:1000',
            'a_down_uri' => 'required|string|max:1000',
            'o_id' => 'required|numeric',
            'a_domain' => 'nullable|json',
            'a_enable' => 'required|numeric|in:0,1',
            'a_limit' => 'required|string',
            'a_replace_packager_name' => 'required|numeric|in:0,1',
            'a_is_compress' => 'required|numeric|in:0,1',
            'a_new_package_name' => 'nullable|string',
        ];

        if ($arrData['a_id']) {
            $arrRules['a_id'] = 'required|numeric';
        }

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $arrADomain =  json_decode($arrData['a_domain'], true);

        if (empty($arrADomain)) {
            return $this->error('301');
        }

        $arrData['a_limit'] = strTimeToSeconds($arrData['a_limit']);

        if ($arrData['a_id']) {
            $ApkModel = ApkModel::where('a_id', $arrData['a_id'])->first();
            if (empty($ApkModel)) {
                return $this->error(993);
            }
        } else {
            $ApkModel = new ApkModel;
        }

        $ApkModel->fill($arrData);

        $ApkModel->save();

        return $this->success();
    }


    /**
     * 删除Apk
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function delApk(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'a_id' => $Request->post('a_id'),
        ];

        $arrRules = [
            'a_id' => 'required|numeric',

        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $ApkModel = ApkModel::where('a_id', $arrData['a_id'])->first();
        if (empty($ApkModel)) {
            return $this->error(993);
        }
        $arrData['deleted_at'] = date('Y-m-d H:i:s');
        $arrData['a_status'] = 1;

        $ApkModel->fill($arrData);

        $ApkModel->save();

        return $this->success();
    }


    /**
     * 重新打包
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function repackageApk(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'a_id' => $Request->post('a_id'),
            'replace_bucket' => $Request->post('replace_bucket'),
        ];

        $arrRules = [
            'a_id' =>  'required|numeric',
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $MessageCore = new MessageCore();

        $arrTask = [
            'callback' => [ApkPackageLogic::class, 'repackageApk'],
            'data' => [
                'a_id' => $arrData['a_id'],
                'replace_bucket' => $arrData['replace_bucket'],
                'msg_id' => $MessageCore->getMsgQueueKey(),
            ]
        ];

        // $arrTask = [
        //     'callback' => [StorageLogic::class, 'clearOssFile'],
        //     'data' => [
        //         'a_id' => $arrData['a_id'],
        //         'replace_bucket' => $arrData['replace_bucket'],
        //         'msg_id' => $MessageCore->getMsgQueueKey(),
        //     ]
        // ];

        $TaskCore = new TaskCore();

        $TaskCore->set($arrTask);

        return $this->success([
            'msg_id' => $MessageCore->getMsgQueueKey(),
        ]);
    }



    /**
     * OSS列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getOssList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $OssModel = OssModel::from('oss as o')
            ->orderBy('o.o_id', 'desc')
            ->whereNull('o.deleted_at')
            ->paginate($Request->get('limit'),  [
                'o.*',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $OssModel->currentPage(),
            'total_page' => $OssModel->lastPage(),
            'limit' => $OssModel->perPage(),
            'item' => $OssModel->items(),
            'total' => $OssModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 设置OSS存储
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function saveOss(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'o_id' => $Request->post('o_id'),
            'o_name' => $Request->post('o_name'),
            'o_quicken' => $Request->post('o_quicken'),
            'o_type' => $Request->post('o_type'),
            'o_key_id' => $Request->post('o_key_id'),
            'o_key_secret' => $Request->post('o_key_secret'),
            'o_app_id' => $Request->post('o_app_id'),
            'o_region' => $Request->post('o_region'),
            'o_host' => (string)$Request->post('o_host'),
        ];

        $arrRules = [
            'o_name' => 'required|string|max:1000',
            'o_quicken' => 'required|numeric|in:0,1',
            'o_type' => 'required|string|max:1000',
            'o_key_id' => 'required|string|max:1000',
            'o_key_secret' => 'required|string|max:1000',
            'o_region' => 'required|string|max:1000',
        ];

        if ($arrData['o_type'] == 'tencent') {
            $arrRules['o_app_id'] = 'required|string|max:1000';
        } else {
            $arrData['o_app_id'] = (string)$arrData['o_app_id'];
        }

        if ($arrData['o_type'] == 'minio') {
            $arrRules['o_host'] = 'required|string|max:1000';
            $arrData['o_quicken'] = 0;
        }

        if ($arrData['o_id']) {
            $arrRules['o_id'] = 'required|numeric';
        }

        $arrTencentRegion = [
            "ap-hongkong" => "中国香港",
            "ap-singapore" => "新加坡",
            "ap-seoul" => "首尔",
            "ap-bangkok" => "曼谷",
            "ap-tokyo" => "东京",
        ];

        $arrS3Region = [
            'ap-east-1' => '香港',
            'ap-northeast-3' => '大阪',
            'ap-northeast-2' => '首尔',
            'ap-southeast-1' => '新加坡',
            'ap-northeast-1' => '东京'
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        if ($arrData['o_id']) {
            $OssModel = OssModel::where('o_id', $arrData['o_id'])->first();
            if (empty($OssModel)) {
                return $this->error(993);
            }
        } else {
            $OssModel = new OssModel;
        }

        $OssModel->fill($arrData);

        try {
            $strTempBucket = 'temp' . mt_rand(1000, 9999);
            if ($arrData['o_type'] == 's3') {
                $OssCore =  new AmazonOssCore($arrData['o_key_id'], $arrData['o_key_secret'], $arrData['o_region']);
            } else if ($arrData['o_type'] == 'tencent') {
                $OssCore = new TencentCosCore($arrData['o_key_id'], $arrData['o_key_secret'], $arrData['o_region']);
                $strTempBucket  .= '-' . $OssModel->o_app_id;
            } else if ($arrData['o_type'] == 'minio') {
                $OssCore = new MinIOCore($arrData['o_key_id'], $arrData['o_key_secret'], $arrData['o_region'], $arrData['o_host']);
            }

            $OssCore->createBucket($strTempBucket);
            $OssCore->delBucket($strTempBucket);
        } catch (\Exception $e) {
            return $this->error('200', '', [], [$e->getMessage()]);
        }

        $OssModel->save();

        return $this->success();
    }

    /**
     * 删除OSS存储
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function delOss(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'o_id' => $Request->post('o_id'),
        ];

        $arrRules = [
            'o_id' => 'required|numeric',
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $OssModel = OssModel::where('o_id', $arrData['o_id'])->first();
        if (empty($OssModel)) {
            return $this->error(993);
        }
        $arrData['deleted_at'] = date('Y-m-d H:i:s');
        $arrData['o_status'] = 1;
        $OssModel->fill($arrData);

        $OssModel->save();

        return $this->success();
    }

    /**
     * 获取存储桶列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getBucketList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $OssBucketModel = OssBucketModel::from('oss_bucket as ob')
            ->leftjoin('apk as a', 'a.a_id', 'ob.a_id')
            ->orderBy('ob.ob_id', 'desc')
            ->whereNull('ob.deleted_at')
            ->paginate($Request->get('limit'),  [
                // 'ob.*',
                'a.a_name',
                'ob.ob_id',
                'ob.ob_status',
                'ob.ob_sort',
                'ob.ob_name',
                'ob.ob_location',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $OssBucketModel->currentPage(),
            'total_page' => $OssBucketModel->lastPage(),
            'limit' => $OssBucketModel->perPage(),
            'item' => $OssBucketModel->items(),
            'total' => $OssBucketModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 删除OSS存储桶
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function delBucket(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'ob_id' => $Request->post('ob_id'),
        ];

        $arrRules = [
            'ob_id' => 'required|numeric',
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $OssBucketModel = OssBucketModel::where('ob_id', $arrData['ob_id'])->first();
        if (empty($OssBucketModel)) {
            return $this->error(993);
        }
        $arrData['deleted_at'] = date('Y-m-d H:i:s');
        $arrData['ob_status'] = 1;
        $OssBucketModel->fill($arrData);

        $OssBucketModel->save();

        return $this->success();
    }


    /**
     * 获取存储桶文件列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getBucketFileList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $OssBucketFileModel = OssBucketFileModel::from('oss_bucket_file as obf')
            ->leftjoin('oss_bucket as ob', 'ob.ob_id', 'obf.ob_id')
            ->leftjoin('apk as a', 'a.a_id', 'obf.a_id')
            ->orderBy('obf.obf_id', 'desc')
            ->whereNull('obf.deleted_at')
            ->paginate($Request->get('limit'),  [
                // 'obf.*',
                'a.a_name',
                'obf.obf_id',
                'obf.obf_status',
                'obf.obf_sort',
                'ob.ob_name',
                'ob.ob_location',
                'obf.obf_key',

            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $OssBucketFileModel->currentPage(),
            'total_page' => $OssBucketFileModel->lastPage(),
            'limit' => $OssBucketFileModel->perPage(),
            'item' => $OssBucketFileModel->items(),
            'total' => $OssBucketFileModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 删除OSS存储桶
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function delBucketFile(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'obf_id' => $Request->post('obf_id'),
        ];

        $arrRules = [
            'obf_id' => 'required|numeric',
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $OssBucketFileModel = OssBucketFileModel::where('obf_id', $arrData['obf_id'])->first();
        if (empty($OssBucketFileModel)) {
            return $this->error(993);
        }
        $arrData['deleted_at'] = date('Y-m-d H:i:s');
        $arrData['obf_status'] = 1;
        $OssBucketFileModel->fill($arrData);

        $OssBucketFileModel->save();

        return $this->success();
    }

    /**
     * 获取报表列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getReportList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $ReportModel = ReportModel::from('report as r')
            ->leftjoin('apk as a', 'a.a_id', 'r.a_id')
            ->orderBy('r.r_id', 'desc')
            ->whereNull('r.deleted_at')
            ->paginate($Request->get('limit'),  [
                'a.a_name',
                'r.r_date',
                'r.r_num',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $ReportModel->currentPage(),
            'total_page' => $ReportModel->lastPage(),
            'limit' => $ReportModel->perPage(),
            'item' => $ReportModel->items(),
            'total' => $ReportModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 查看OSS存储详情-测试调试接口
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getBucketInfo(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $OssModel = OssModel::whereOId(1)->first();

        $OssCore = new TencentCosCore($OssModel->o_key_id, $OssModel->o_key_secret, $OssModel->o_region);

        $Result = $OssCore->getBucketAccelerate('2a3tna-1321054239')->toArray();
        print_r($Result);

        $Result = $OssCore->getBucketAccelerate('leeah5-1321054239');
        print_r($Result);

        $Result = $OssCore->setBucketAccelerate('leeah5-1321054239', true);
        print_r($Result);
        exit;
    }



    /**
     * APK下载链接
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getApkDownLink(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {

        $ApkModelAll = ApkModel::from('apk as a')
            ->leftJoin('oss as o', 'o.o_id', '=', 'a.o_id')
            ->orderBy('a.a_id', 'desc')
            ->whereNull('a.deleted_at')
            ->get();

        $arrResult = [];

        // print_r($ApkModelAll->toArray());

        foreach ($ApkModelAll as $ApkModel) {

            foreach ($ApkModel->a_domain as $intKey => $strDomain)
                $arrResult[] = [
                    'a_name' => $ApkModel->a_name,
                    'a_package_name' => $ApkModel->a_package_name,
                    'a_down_uri' => $ApkModel->a_down_uri,
                    'a_domain' => $strDomain,
                    'down_link' => $ApkModel->down_link[$intKey],
                ];
        }

        return $this->success($arrResult);
    }
}

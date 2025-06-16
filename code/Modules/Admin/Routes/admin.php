<?php

use App\Models\AdminUsersModel;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\SystemController;
use Modules\Admin\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AppController;

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| 规范：
| 1、根据系统模块划分路由
| 2、路由地址必须小写
| 3、路由执行handle必须采用数组回调类方式
| 4、路由名必须定义，路由名字为`控制器@方法名`
*/

Route::prefix('test')
    ->group(function () {
        Route::get('/bucket/info', [AppController::class, 'getBucketInfo'])->name('AppController@getBucketInfo');
    });

# 这是一个调试初始化路由，他将删除id为1的admin用户，id 为1的超级管理员角色，然后创建一个ID为1的超级角色，和在这个角色下创建id为1的用户，并且将所有权限复制给超级管理员
# 需要注释，
Route::prefix('system')
    ->group(function () {
        Route::get('/initadminuser', function (Request $Request) {
            AdminUsersModel::initSuperAdmin();
            return 'success';
        });
    });


# 登录/退出
Route::prefix('system')
    ->group(function () {
        # 系统用户管理登录/退出
        Route::prefix('user')
            ->group(function () {
                Route::post('/login', [SystemController::class, 'login'])->name('SystemController@login');
                Route::post('/logout', [SystemController::class, 'logout'])->name('SystemController@logout');
            });
    });


# 系统管理
Route::prefix('system')
    ->middleware(AuthMiddleware::class)
    ->group(function () {
        # 系统用户
        Route::prefix('user')
            ->group(function () {
                Route::get('/list', [SystemController::class, 'getAdminUserList'])->name('SystemController@getAdminUserList');
                Route::post('/save', [SystemController::class, 'saveAdminUser'])->name('SystemController@saveAdminUser');
                Route::post('/del', [SystemController::class, 'delAdminUser'])->name('SystemController@delAdminUser');
                Route::get('/role/node', [SystemController::class, 'getCurrentAdminUserRoleNode'])->name('SystemController@getCurrentAdminUserRoleNode');
            });

        # 角色
        Route::prefix('role')
            ->group(function () {
                Route::get('/list', [SystemController::class, 'getAdminRoleList'])->name('SystemController@getAdminRoleList');
                Route::post('/save', [SystemController::class, 'saveAdminRole'])->name('SystemController@saveAdminRole');
                Route::post('/del', [SystemController::class, 'delAdminRole'])->name('SystemController@delAdminRole');
            });

        # 节点
        Route::prefix('node')
            ->group(function () {
                Route::get('/list', [SystemController::class, 'getAdminNodeList'])->name('SystemController@getAdminNodeList');
                Route::post('/save', [SystemController::class, 'saveAdminNode'])->name('SystemController@saveAdminNode');
            });

        # 配置
        Route::prefix('config')
            ->group(function () {
                Route::get('/list', [SystemController::class, 'getAdminConfigList'])->name('SystemController@getAdminConfigList');
                Route::post('/save', [SystemController::class, 'saveAdminConfig'])->name('SystemController@saveAdminConfig');
            });

        # 计划任务
        Route::prefix('task')
            ->group(function () {
                Route::get('/list', [SystemController::class, 'getPlanTaskList'])->name('SystemController@getPlanTaskList');
                Route::post('/save', [SystemController::class, 'savePlanTask'])->name('SystemController@savePlanTask');
            });

        # 消息管理
        Route::prefix('msg')
            ->group(function () {
                Route::get('/task/list', [SystemController::class, 'getTaskMsg'])->name('SystemController@getTaskMsg');
            });
    });

# APP管理
Route::prefix('app')
    ->middleware(AuthMiddleware::class)
    ->group(function () {
        # package
        Route::prefix('apk')
            ->group(function () {
                Route::get('/list', [AppController::class, 'getApkList'])->name('AppController@getApkList');
                Route::get('/down/list', [AppController::class, 'getApkDownLink'])->name('AppController@getApkDownLink');
                Route::post('/save', [AppController::class, 'saveApk'])->name('AppController@saveApk');
                Route::post('/upload', [AppController::class, 'uploadApk'])->name('AppController@uploadApk');
                Route::post('/repackage', [AppController::class, 'repackageApk'])->name('AppController@repackageApk');
                Route::post('/del', [AppController::class, 'delApk'])->name('AppController@delApk');
            });
    });

# 存储管理
Route::prefix('storage')
    ->middleware(AuthMiddleware::class)
    ->group(function () {
        # OSS
        Route::prefix('oss')
            ->group(function () {
                Route::get('/list', [AppController::class, 'getOssList'])->name('AppController@getOssList');
                Route::post('/save', [AppController::class, 'saveOss'])->name('AppController@saveOss');
                Route::post('/del', [AppController::class, 'delOss'])->name('AppController@delOss');
            });

        # Bucket
        Route::prefix('bucket')
            ->group(function () {
                Route::get('/list', [AppController::class, 'getBucketList'])->name('AppController@getBucketList');
                Route::post('/del', [AppController::class, 'delBucket'])->name('AppController@delBucket');
            });

        # File
        Route::prefix('file')
            ->group(function () {
                Route::get('/list', [AppController::class, 'getBucketFileList'])->name('AppController@getBucketFileList');
                Route::post('/del', [AppController::class, 'delBucketFile'])->name('AppController@delBucketFile');
            });
    });


# 报表
Route::prefix('report')
    ->middleware(AuthMiddleware::class)
    ->group(function () {
        # down
        Route::prefix('down')
            ->group(function () {
                Route::get('/list', [AppController::class, 'getReportList'])->name('AppController@getReportList');
            });
    });

<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\AdminNodeModel;
use App\Models\AdminRoleModel;
use App\Models\AdminConfigModel;
use App\Models\AdminRoleNodeModel;
use App\Models\AdminUsersModel;
use App\Models\PlanTaskModel;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Http\Middleware\BaseController;
use Modules\Admin\Http\Middleware\HttpException;
use TaskManager\MessageCore;

class SystemController extends BaseController
{
    /**
     * 登录
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function login(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'au_name' => 'required',
            'au_pwd' =>  'required',
        ];

        $arrData = [
            'au_name' => $Request->post('au_name'),
            'au_pwd' => $Request->post('au_pwd'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminUsersModel = AdminUsersModel::where('au_name', $arrData['au_name'])->first();

        if (empty($AdminUsersModel) || md5($arrData['au_pwd']) != $AdminUsersModel->au_pwd) {
            return $this->error('101');
        }

        $strToken = $AdminUsersModel->setTokenToCache();

        return $this->success([
            'admin_token' => $strToken,
        ]);
    }

    /**
     * 退出
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function logout(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $strToken = $Request->header('admin-token');

        if ($strToken) {
            Cache::delete($strToken);
        }

        return $this->success();
    }

    /**
     * 系统用户列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getAdminUserList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrData = [
            'page' => $Request->get('page'),
            'limit' => $Request->get('limit'),
            'key' => $Request->get('key'),
        ];

        $Validator = Validator::make($arrData, [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminUsersModel = AdminUsersModel::from('admin_users as au')
            ->orderBy('au.au_id', 'desc')
            ->whereNull('au.deleted_at');

        if ($arrData['key'] !== null) {
            $strKey = $arrData['key'];
            $AdminUsersModel = $AdminUsersModel->where(function ($Query) use ($strKey) {
                $Query->orWhere('au.au_name', 'like', '%' . $strKey . '%')
                    ->orWhere('au.au_id', 'like', '%' . $strKey . '%');
            });
        }

        $AdminUsersModel = $AdminUsersModel
            ->paginate($arrData['limit'],  [
                'au.*',
            ], '', $arrData['page']);

        $arrResult = [
            'page' => $AdminUsersModel->currentPage(),
            'total_page' => $AdminUsersModel->lastPage(),
            'limit' => $AdminUsersModel->perPage(),
            'item' => $AdminUsersModel->items(),
            'total' => $AdminUsersModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 设置系统用户
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function saveAdminUser(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'au_name' => 'required',
            'ar_id' => 'required|numeric',
        ];

        $arrData = [
            'au_name' => $Request->post('au_name'),
            'ar_id' => $Request->post('ar_id'),
            'au_id' => $Request->post('au_id'),
            'au_pwd' => $Request->post('au_pwd'),
        ];

        if (empty($arrData['au_id'])) {
            $arrRules['au_pwd'] = 'required';
        }

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        if (AdminUsersModel::where('au_name', $arrData['au_name'])->exists() && empty($arrData['au_id'])) {
            throw new HttpException('100');
        }

        if ($arrData['au_id']) {
            $AdminUsersModel = AdminUsersModel::where('au_id', $arrData['au_id'])->first();
        } else {
            $AdminUsersModel = new AdminUsersModel;
        }

        if ($arrData['au_pwd']) {
            $arrData['au_pwd'] = md5($arrData['au_pwd']);
        } else {
            unset($arrData['au_pwd']);
        }

        $AdminUsersModel->fill($arrData);
        $AdminUsersModel->save();

        return $this->success();
    }

    /**
     * 删除系统用户
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function delAdminUser(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'au_id' => 'required',
        ];

        $arrData = [
            'au_id' => $Request->post('au_id'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $arrData['au_id'] = explode(',', $arrData['au_id']);

        AdminUsersModel::whereIn('au_id', $arrData['au_id'])->delete();

        return $this->success();
    }

    /**
     * 角色列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getAdminRoleList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminRoleModel = AdminRoleModel::from('admin_role as ar')
            ->orderBy('ar.ar_id', 'desc')
            ->whereNull('ar.deleted_at')
            ->paginate($Request->get('limit'),  [
                'ar.*',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $AdminRoleModel->currentPage(),
            'total_page' => $AdminRoleModel->lastPage(),
            'limit' => $AdminRoleModel->perPage(),
            'item' => $AdminRoleModel->items(),
            'total' => $AdminRoleModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 添加|更改角色
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function saveAdminRole(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'ar_name' => 'required',
        ];

        $arrData = [
            'ar_name' => $Request->post('ar_name'),
            'ar_id'   => $Request->post('ar_id'),
            'an_id'   => $Request->post('an_id'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        if (!empty($arrData['ar_id'])) {
            $AdminRoleModel = AdminRoleModel::where('ar_id', $arrData['ar_id'])->first();
        } else {
            $AdminRoleModel = new AdminRoleModel;
        }

        // 删除角色id不然添加报错
        $strAnId = $arrData['an_id'];
        unset($arrData['an_id']);

        $AdminRoleModel->fill($arrData);
        $AdminRoleModel->save();

        if (!empty($strAnId)) {
            $arrAnId = explode(',', $strAnId);
            $AdminRoleModel->updateRoleNode($arrAnId);
            $AdminRoleModel->clearRoleCache();
        }

        return $this->success();
    }

    /**
     * 删除角色
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function delAdminRole(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'ar_id' => 'required',
        ];

        $arrData = [
            'ar_id' => $Request->post('ar_id'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $arrData['ar_id'] = explode(',', $arrData['ar_id']);

        // 如果有人使用这个角色那么禁止删除
        $arrAdminUsers = AdminUsersModel::whereIn('ar_id', $arrData['ar_id'])->select('au_name')->get()->toArray();
        $arrAdminUsers = array_column($arrAdminUsers, 'au_name');
        if ($arrAdminUsers) {
            return $this->error(103, "", [], [implode(',', $arrAdminUsers)]);
        }

        // 删除角色和关系表的数据
        AdminRoleModel::whereIn('ar_id', $arrData['ar_id'])->delete();
        AdminRoleNodeModel::whereIn('ar_id', $arrData['ar_id'])->delete();

        return $this->success();
    }

    /**
     * 节点列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getAdminNodeList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminNodeModel = AdminNodeModel::from('admin_node as an')
            ->orderBy('an.an_id', 'desc')
            ->whereNull('an.deleted_at')
            ->paginate($Request->get('limit'),  [
                'an.*',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $AdminNodeModel->currentPage(),
            'total_page' => $AdminNodeModel->lastPage(),
            'limit' => $AdminNodeModel->perPage(),
            'item' => $AdminNodeModel->items(),
            'total' => $AdminNodeModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 设置节点
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function saveAdminNode(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'an_name' => 'required',
            'an_id'   => 'required',
        ];

        $arrData = [
            'an_name'      => $Request->post('an_name'),
            'an_code'      => (string)$Request->post('an_code'),
            'an_id'        => $Request->post('an_id'),
            'an_pid'       => $Request->post('an_pid'),
            'an_is_dir'    => $Request->post('an_is_dir'),
            'an_is_hidden' => $Request->post('an_is_hidden'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminNodeModel = AdminNodeModel::where('an_id', $arrData['an_id'])->first();
        if (empty($AdminNodeModel)) {
            return $this->error(103);
        }

        $AdminNodeModel->fill($arrData);
        $AdminNodeModel->save();

        return $this->success();
    }


    /**
     * 系统配置列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getAdminConfigList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminConfigModel = AdminConfigModel::from('admin_config as ac')
            ->orderBy('ac.ac_id', 'desc')
            ->whereNull('ac.deleted_at')
            ->paginate($Request->get('limit'),  [
                'ac.*',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $AdminConfigModel->currentPage(),
            'total_page' => $AdminConfigModel->lastPage(),
            'limit' => $AdminConfigModel->perPage(),
            'item' => $AdminConfigModel->items(),
            'total' => $AdminConfigModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 设置系统配置
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function saveAdminConfig(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'ac_code' => 'required',
            'ac_val' => 'required',
            'ac_description' => 'required',
            'ac_id' => 'required|numeric',
        ];

        $arrData = [
            'ac_code' => $Request->post('ac_code'),
            'ac_val' => $Request->post('ac_val'),
            'ac_description' => $Request->post('ac_description'),
            'ac_id' => $Request->post('ac_id'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $AdminConfigModel = AdminConfigModel::where('ac_id', $arrData['ac_id'])->first();

        if (empty($AdminConfigModel)) {
            return $this->error(993);
        }

        $AdminConfigModel->fill($arrData);
        $AdminConfigModel->save();

        # 这里需要刷新缓存配置
        return $this->success();
    }

    /**
     * 计划任务列表
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getPlanTaskList(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $Validator = Validator::make($Request->query(), [
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $PlanTaskModel = PlanTaskModel::from('plan_task as pt')
            ->orderBy('pt.pt_id', 'desc')
            ->whereNull('pt.deleted_at')
            ->paginate($Request->get('limit'),  [
                'pt.*',
            ], '', $Request->get('page'));

        $arrResult = [
            'page' => $PlanTaskModel->currentPage(),
            'total_page' => $PlanTaskModel->lastPage(),
            'limit' => $PlanTaskModel->perPage(),
            'item' => $PlanTaskModel->items(),
            'total' => $PlanTaskModel->total(),
        ];

        return $this->success($arrResult);
    }

    /**
     * 设置计划任务
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function savePlanTask(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'pt_id' => 'required|numeric',
            'pt_enable' => 'required|numeric',
            'pt_limit' => 'required|string',
        ];

        $arrData = [
            'pt_id' => $Request->post('pt_id'),
            'pt_enable' => $Request->post('pt_enable'),
            'pt_limit' => $Request->post('pt_limit'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $arrData['pt_limit'] = strTimeToSeconds($arrData['pt_limit']);

        $PlanTaskModel = PlanTaskModel::where('pt_id', $arrData['pt_id'])->first();

        if (empty($PlanTaskModel)) {
            return $this->error(993);
        }

        $PlanTaskModel->fill($arrData);
        $PlanTaskModel->save();


        return $this->success();
    }



    /**
     * 获取异步消息
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getTaskMsg(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrRules = [
            'msg_id' => 'required|string',
        ];

        $arrData = [
            'msg_id' => $Request->get('msg_id'),
        ];

        $Validator = Validator::make($arrData, $arrRules);

        if ($Validator->fails()) {
            return $this->errorValidator($Validator);
        }

        $MessageCore = new MessageCore($arrData['msg_id']);

        $arrMsg = [];
        while (true) {
            $strMsg = $MessageCore->get();
            if (empty($strMsg)) break;

            $arrMsg[] = json_decode($strMsg);
        }
        return $this->success($arrMsg);
    }

    /**
     * 当前用户权限节点
     *
     * @param Request $Request
     * @param AdminUsersModel|null $AdminUsersModel
     * @return Renderable
     */
    public function getCurrentAdminUserRoleNode(Request $Request, ?AdminUsersModel $AdminUsersModel)
    {
        $arrAnIds = AdminRoleNodeModel::whereArId($AdminUsersModel->ar_id)->pluck('an_id');
        $arrResult = [
            'an_ids' => $arrAnIds,
        ];
        return $this->success($arrResult);
    }
}

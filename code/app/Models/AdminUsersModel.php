<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\AdminUsersModel
 *
 * @property int $au_id 后端用户ID
 * @property string $au_name 用户名
 * @property string $au_pwd 密码
 * @property int $ar_id 后端角色ID
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereArId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereAuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereAuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereAuPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUsersModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminUsersModel extends Model
{
    use HasFactory;

    protected $table = 'admin_users';

    protected $primaryKey = 'au_id';

    protected $guarded = [];

    protected $hidden = ['au_pwd', 'deleted_at'];

    protected $appends = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $Date)
    {
        return $Date->format('Y-m-d H:i:s');
    }

    /**
     * 初始化超级管理员
     *
     * @return bool
     */
    public static function initSuperAdmin()
    {
        AdminRoleNodeModel::where('ar_id', 1)->forceDelete();
        AdminRoleModel::where('ar_id', 1)->forceDelete();
        AdminUsersModel::where('au_id', 1)->forceDelete();

        AdminUsersModel::insert([
            'au_id' => 1,
            'au_name' => 'admin',
            'au_pwd' => md5('qq123123'),
            'ar_id' => 1,
        ]);

        AdminRoleModel::insert([
            'ar_name' => '超级管理员',
            'ar_id' => 1,
        ]);

        $AdminNodeModelAll = AdminNodeModel::all();
        foreach ($AdminNodeModelAll as $AdminNodeModel) {
            AdminRoleNodeModel::insert([
                'ar_id' => 1,
                'an_id' => $AdminNodeModel->an_id,
            ]);
        }

        self::getRoleNodeByArId(1, true);

        return true;
    }

    /**
     * 根据TOKEN获取用户缓存模型
     *
     * @param string|null $strToken
     * @return AdminUsersModel|null
     */
    public static function getCacheAdminUserByToken(?string $strToken): ?AdminUsersModel
    {
        if (empty($strToken)) {
            return NULL;
        }

        $intAuId = Cache::get($strToken);
        if (empty($intAuId)) {
            return NULL;
        }

        Cache::set($strToken, $intAuId, config('auth.password_timeout'));

        return self::getCacheAdminUserByAuId($intAuId);
    }

    /**
     * 根据用户ID获取用户缓存数据
     *
     * @param int $intAuId
     * @param boolean $boolFirst
     * @return AdminUsersModel|null
     */
    public static function getCacheAdminUserByAuId($intAuId, $boolFirst = true): ?AdminUsersModel
    {
        $strCacheKey = 'auid-' . $intAuId;

        $AdminUsersModel = Cache::get($strCacheKey);

        if (empty($AdminUsersModel)) {
            if ($boolFirst) {
                self::refreshAdminUserCacheByAuId($intAuId);
                return self::getCacheAdminUserByAuId($intAuId, false);
            }
            return NULL;
        }

        return $AdminUsersModel;
    }

    /**
     * 根据用户ID刷新用户缓存数据
     *
     * @param int $intAuId
     * @return boolean
     */
    public static function refreshAdminUserCacheByAuId($intAuId): bool
    {
        $strCacheKey = 'auid-' . $intAuId;

        $AdminUsersModel = self::where('au_id', $intAuId)->first();

        if (empty($AdminUsersModel)) {
            return false;
        }

        Cache::set($strCacheKey, $AdminUsersModel, config('auth.password_timeout'));

        return true;
    }

    /**
     * 根据角色ID获取缓存里的用户权限
     *
     * @param integer $intArId
     * @return array
     */
    public static function getRoleNodeByArId(int $intArId, bool $boolForceRefresh = false)
    {
        $strCacheKey = 'arid-' . $intArId;

        $arrAdminRoleNode = Cache::get($strCacheKey);

        if ($arrAdminRoleNode === NULL || $boolForceRefresh) {
            $arrAdminRoleNode = AdminRoleNodeModel::from('admin_role_node as arn')
                ->leftJoin('admin_node AS an', 'an.an_id', '=', 'arn.an_id')
                ->where('arn.ar_id', $intArId)
                ->whereNull('arn.deleted_at')
                ->whereNull('an.deleted_at')
                ->select('an.an_code')
                ->get()
                ->toArray();
            $arrAdminRoleNode = array_column($arrAdminRoleNode, 'an_code');
            Cache::set($strCacheKey, $arrAdminRoleNode);
        }

        return $arrAdminRoleNode;
    }

    /**
     * 检测用户是否拥有当前路由的请求权限
     *
     * @param string $strRouteName
     * @return bool
     */
    public function checkAuth($strRouteName)
    {
        $arrAdminRoleNode = self::getRoleNodeByArId($this->ar_id, false);

        return in_array($strRouteName, $arrAdminRoleNode);
    }

    /**
     * 登录写入token
     *
     * @return string
     */
    public function setTokenToCache()
    {
        $strToken = Str::random(32);
        Cache::set($strToken, $this->au_id, config('auth.password_timeout'));
        return $strToken;
    }
}

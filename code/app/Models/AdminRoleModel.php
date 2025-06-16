<?php

namespace App\Models;

use AdminNode;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\AdminRoleModel
 *
 * @property int $ar_id 后端角色ID
 * @property string $ar_name 角色名
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel whereArId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel whereArName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleModel whereUpdatedAt($value)
 * @property-read mixed $an_ids
 * @mixin \Eloquent
 */
class AdminRoleModel extends Model
{
    use HasFactory;

    protected $table = 'admin_role';

    protected $primaryKey = 'ar_id';

    protected $guarded = [];

    protected $hidden = ['deleted_at'];

    protected $appends = [
        'an_ids'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $Date)
    {
        return $Date->format('Y-m-d H:i:s');
    }

    public function getAnIdsAttribute()
    {
        return  AdminRoleNodeModel::whereArId($this->ar_id)->pluck('an_id');
    }

    /**
     * 传入节点更新对应的权限
     *
     * @return void
     */
    public function updateRoleNode($arrAnId)
    {
        AdminRoleNodeModel::where('ar_id', $this->ar_id)->delete();
        $arrAnIds = AdminNodeModel::whereIn('an_id', $arrAnId)->pluck('an_id');

        $arrInsert = [];
        foreach ($arrAnIds as $intAnId) {
            $arrInsert[] = [
                'ar_id' => $this->ar_id,
                'an_id' => $intAnId,
            ];
        }
        AdminRoleNodeModel::insert($arrInsert);
    }

    /**
     * 根据角色ID清空对应的权限缓存
     *
     * @return void
     */
    public function clearRoleCache()
    {
        AdminUsersModel::getRoleNodeByArId($this->ar_id, true);
    }
}

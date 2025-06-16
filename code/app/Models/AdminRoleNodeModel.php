<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\AdminRoleNodeModel
 *
 * @property int $arn_id 后端角色权限节点关系ID
 * @property int $ar_id 后端角色ID
 * @property int $an_id 后端权限节点ID
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel whereAnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel whereArId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel whereArnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRoleNodeModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminRoleNodeModel extends Model
{
    use HasFactory;

    protected $table = 'admin_role_node';

    protected $primaryKey = 'arn_id';

    protected $guarded = [];

    protected $hidden = [];

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
}

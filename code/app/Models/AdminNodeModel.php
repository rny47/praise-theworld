<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\AdminNodeModel
 *
 * @property int $an_id 后端权限节点ID
 * @property int $an_pid 后端权限节点父级ID
 * @property string $an_name 权限节点名称
 * @property string $an_code 权限节点后端路由名
 * @property int $an_is_dir 是否为目录,0=否,1=是
 * @property int $an_is_hidden 是否隐藏,0=否,1=是
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereAnCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereAnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereAnIsDir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereAnIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereAnName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereAnPid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNodeModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminNodeModel extends Model
{
    use HasFactory;

    protected $table = 'admin_node';

    protected $primaryKey = 'an_id';

    protected $guarded = [];

    protected $hidden = ['deleted_at'];

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

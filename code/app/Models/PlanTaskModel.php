<?php

namespace App\Models;

use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\PlanTaskModel
 *
 * @property int $pt_id id
 * @property string $pt_name 任务名
 * @property string $pt_code 任务编码
 * @property int $pt_enable 任务开关
 * @property string $pt_limit 任务频率
 * @property \Illuminate\Support\Carbon $pt_last_exec 最后执行时间时间
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel wherePtCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel wherePtEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel wherePtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel wherePtLastExec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel wherePtLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel wherePtName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanTaskModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlanTaskModel extends Model
{
    use HasFactory;

    protected $table = 'plan_task';

    protected $primaryKey = 'pt_id';

    protected $guarded = [];

    protected $hidden = ['au_pwd', 'deleted_at'];

    protected $appends = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'pt_last_exec' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $Date)
    {
        return $Date->format('Y-m-d H:i:s');
    }

    /**
     * 转换成时分秒格式
     *
     * @param int $intPtLimit
     * @return string
     */
    public function getPtLimitAttribute($intPtLimit)
    {
        return secondsToStrTime($intPtLimit);
    }
}

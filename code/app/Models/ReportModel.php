<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReportModel
 *
 * @property int $r_id id
 * @property int $a_id APK ID
 * @property int $r_date 时间
 * @property int $r_num 计数
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereAId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereRDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereRId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereRNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportModel extends Model
{
    use HasFactory;

    protected $table = 'report';

    protected $primaryKey = 'r_id';

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

    /**
     * 转换成yyyy-mon-day 格式
     *
     * @param int $intRDate
     * @return string
     */
    public function getRDateAttribute($intRDate)
    {
        return date('Y-m-d', strtotime($intRDate));
    }
}

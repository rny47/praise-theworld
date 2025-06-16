<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OssBucketModel
 *
 * @property int $ob_id id
 * @property int $ob_status 状态：0=正常,1=删除,2=过度
 * @property int $a_id APK ID
 * @property int $o_id OSS ID
 * @property \Illuminate\Support\Carbon $ob_sort 优先级
 * @property string $ob_name 名字
 * @property string $ob_location 地区
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereAId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereOId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereObId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereObLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereObName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereObSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereObStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OssBucketModel extends Model
{
    use HasFactory;

    protected $table = 'oss_bucket';

    protected $primaryKey = 'ob_id';

    protected $guarded = [];

    protected $hidden = [];

    protected $appends = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'ob_sort' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $Date)
    {
        return $Date->format('Y-m-d H:i:s');
    }
}

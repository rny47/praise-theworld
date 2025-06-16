<?php

namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TencentCore\TencentCosCore;
use Amazon\AmazonOssCore;

/**
 * App\Models\FileCleanModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel query()
 * @property int $fc_id id
 * @property string $fc_file 文件名称
 * @property int $fc_deltime 计划删除时间
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel whereFcDeltime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel whereFcFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel whereFcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileCleanModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileCleanModel extends Model
{
    use HasFactory;

    protected $table = 'file_clean';

    protected $primaryKey = 'fc_id';

    protected $guarded = [];

    protected $hidden = [];

    protected $appends = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];
}

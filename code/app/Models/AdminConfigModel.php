<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\AdminConfigModel
 *
 * @property int $ac_id id
 * @property string $ac_code 配置编码
 * @property string|null $ac_val 配置值
 * @property string|null $ac_description 配置备注
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereAcCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereAcDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereAcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereAcVal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminConfigModel extends Model
{
    use HasFactory;

    protected $table = 'admin_config';

    protected $primaryKey = 'ac_id';

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

    /**
     * 根据编码获取配置
     *
     * @param string $strCode
     * @return string|null
     */
    static public function getAcValByAcCode($strAcCode)
    {
        $strKey = 'ac-key-' . $strAcCode;

        $strAcVal = Cache::get($strKey);
        if ($strAcVal === null) {
            $strAcVal = self::where('ac_code', $strAcCode)->value('ac_val');
            if ($strAcVal !== null) {
                Cache::put($strKey, $strAcVal, 3600);
            }
        }

        return $strAcVal;
    }

    /**
     * 更新缓存
     *
     * @return bool
     */
    public function refreshCache()
    {
        $strKey = 'ac-key-' . $this->ac_code;
        return  Cache::put($strKey, $this->ac_val, 3600);
    }

    /**
     * 后置触发器
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function (AdminConfigModel $AdminConfigModel) {
            $AdminConfigModel->refreshCache();
        });
    }
}

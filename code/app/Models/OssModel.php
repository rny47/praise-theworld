<?php

namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TencentCore\TencentCosCore;
use Amazon\AmazonOssCore;
use MinOSS\MinIOCore;

/**
 * App\Models\OssModel
 *
 * @property int $o_id id
 * @property string $o_name OSS名字
 * @property int $o_status 状态：0=正常,1=删除,2=过度
 * @property int $o_quicken 开启加速：0=不开启,1=开启
 * @property string $o_type 类型：tencent=腾讯
 * @property string $o_key_id KeyId
 * @property string $o_key_secret KeySecret
 * @property string $o_app_id 桶应用ID
 * @property string $o_region 区域
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOKeySecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOQuicken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereORegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereOType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssModel whereUpdatedAt($value)
 * @property-read string $o_type_name
 * @mixin \Eloquent
 */
class OssModel extends Model
{
    use HasFactory;

    protected $table = 'oss';

    protected $primaryKey = 'o_id';

    protected $guarded = [];

    protected $hidden = [];

    protected $appends = [
        'o_type_name'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    private $arrOTypeNameMap = [
        's3' => '亚马逊云',
        'tencent' => '腾讯云',
        'minio' => 'MinIO',
    ];

    /**
     * 腾讯云COS模型 | 亚马逊云OSS模型
     *
     * @var TencentCosCore|AmazonOssCore
     */
    private $OssCore;

    protected function serializeDate(DateTimeInterface $Date)
    {
        return $Date->format('Y-m-d H:i:s');
    }

    /**
     * 获取腾讯云模型
     *
     * @return TencentCosCore|AmazonOssCore
     */
    public function getCosCore($boolAccelerate = NULL)
    {
        if ($this->OssCore == null) {

            if ($boolAccelerate === NULL) {
                $boolAccelerate = $this->o_quicken == 1;
            }

            if ($this->o_type == 'tencent') {
                $this->OssCore = new TencentCosCore($this->o_key_id, $this->o_key_secret, $this->o_region, $boolAccelerate);
            } else if ($this->o_type == 's3') {
                $this->OssCore = new AmazonOssCore($this->o_key_id, $this->o_key_secret, $this->o_region, $boolAccelerate);
            } else if ($this->o_type == 'minio') {
                $this->OssCore = new MinIOCore($this->o_key_id, $this->o_key_secret, $this->o_region, $this->o_host);
            }
        }
        return $this->OssCore;
    }

    /**
     * 获取公共云名称
     *
     * @return string
     */
    public function getOTypeNameAttribute()
    {
        return $this->arrOTypeNameMap[$this->o_type];
    }
}

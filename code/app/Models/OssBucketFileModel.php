<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;

/**
 * App\Models\OssBucketFileModel
 *
 * @property int $obf_id id
 * @property int $obf_status 状态：0=正常,1=删除,2=过度
 * @property int $a_id APK ID
 * @property int $o_id OSS ID
 * @property int $ob_id OSS 存储桶 ID
 * @property \Illuminate\Support\Carbon $obf_sort 优先级
 * @property string $obf_key 文件KEY
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereAId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereOId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereObId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereObfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereObfKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereObfSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereObfStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OssBucketFileModel orderBy($strColum,$value)
 * @method string \App\Models\OssBucketFileModel getDownUrl($boolUseCache)
 * @mixin \Eloquent
 */
class OssBucketFileModel extends Model
{
    use HasFactory;

    protected $table = 'oss_bucket_file';

    protected $primaryKey = 'obf_id';

    protected $guarded = [];

    protected $hidden = [];

    protected $appends = [];

    /**
     * 存储桶模型
     *
     * @var [OssBucketModel
     */
    private $OssBucketModel;

    /**
     * OSS配置模型
     *
     * @var OssModel
     */
    private $OssModel;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'obf_sort' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $Date)
    {
        return $Date->format('Y-m-d H:i:s');
    }

    /**
     * 获取存储桶模型
     *
     * @return OssBucketModel
     */
    public function getOssBucketModel(): OssBucketModel
    {
        if ($this->OssBucketModel == null) {
            $this->OssBucketModel = OssBucketModel::where('ob_id', $this->ob_id)->firstOrFail();
        }
        return  $this->OssBucketModel;
    }

    /**
     * 获取OSS配置模型
     *
     * @return OssModel
     */
    public function getOssModel(): OssModel
    {
        if ($this->OssModel == null) {
            $this->OssModel = OssModel::where('o_id', $this->o_id)->firstOrFail();
        }
        return  $this->OssModel;
    }

    /**
     * 获取下载地址
     *
     * @param boolean $boolUseCache
     * @return string
     */
    public function getDownUrl($boolUseCache = true, $boolAccelerate = NULL): string
    {
        $strCacheKey = 'os-file-down-url-' . $this->obf_id;

        $strUrl = Redis::connection('cache')->get($strCacheKey);

        if ($boolUseCache == false || $strUrl === null) {
            $strUrl = $this->getOssModel()
                ->getCosCore($boolAccelerate)
                ->getObjectUrl($this->getOssBucketModel()->ob_name, $this->obf_key);

            if ($strUrl !== null) {
                if ($this->getOssModel()->o_type == 'tencent') {
                    $strUrl .= 'response-content-disposition=attachment';
                }
                Redis::connection('cache')->setEx($strCacheKey, 10 * 60, $strUrl);
            }
        }
        return $strUrl;
    }
}

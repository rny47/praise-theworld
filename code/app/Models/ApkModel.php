<?php

namespace App\Models;

use App\Task\Logic\ReportLogic;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TaskManager\TaskCore;

/**
 * App\Models\ApkModel
 *
 * @property int $a_id id
 * @property int $a_status 状态：0=正常,1=删除
 * @property string $a_name 应用名称
 * @property string $a_package_name APK包名
 * @property string $a_save_path APK源包地址
 * @property string $a_down_uri APK下载链接后缀
 * @property int $o_id OSS ID
 * @property string $a_domain 301域名
 * @property int $a_enable 自动打包开关
 * @property string $a_limit 打包频率
 * @property string $a_last_package 最后打包时间
 * @property \Illuminate\Support\Carbon $created_at 添加时间
 * @property \Illuminate\Support\Carbon|null $updated_at 删除时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $a_replace_packager_name 是否替换包名
 * @property-read void $down_link
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereADomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereADownUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereALastPackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereALimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAPackageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAReplacePackagerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereASavePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereOId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereUpdatedAt($value)
 * @property int $a_is_compress 是否压缩包:1=是,0=否
 * @method static \Illuminate\Database\Eloquent\Builder|ApkModel whereAIsCompress($value)
 * @mixin \Eloquent
 */
class ApkModel extends Model
{
    use HasFactory;

    protected $table = 'apk';

    protected $primaryKey = 'a_id';

    protected $guarded = [];

    protected $hidden = [];

    protected $appends = [
        'down_link',
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

    /**
     * 查看下载链接
     *
     * @return void
     */
    public function getDownLinkAttribute()
    {
        $arrDownLink = json_decode($this->attributes['a_domain'], true);
        foreach ($arrDownLink as &$strLink) {
            $strLink = 'https://' . $strLink . '/' . $this->attributes['a_down_uri'];
        }
        return $arrDownLink;
    }

    /**
     * 转换成时分秒格式
     *
     * @param int $intALimit
     * @return string
     */
    public function getALimitAttribute($intALimit)
    {
        return secondsToStrTime($intALimit);
    }

    /**
     * 转换成时分秒格式
     *
     * @param int $intALimit
     * @return string
     */
    public function getALastPackageAttribute($intALastPackage)
    {
        if ($intALastPackage > 0) {
            return date('Y-m-d H:i:s', $intALastPackage);
        } else {
            return NULL;
        }
    }

    /**
     * 域名转数组
     *
     * @param int $strADomain
     * @return string
     */
    public function getADomainAttribute($strADomain)
    {
        return json_decode($strADomain);
    }


    /**
     * 添加文件下载次数
     *
     * @return bool
     */
    public function addDownNum()
    {
        $arrTask = [
            'callback' => [ReportLogic::class, 'addDownNum'],
            'data' => [
                'a_id' => $this->a_id,
            ]
        ];

        $TaskCore = new TaskCore();

        $TaskCore->set($arrTask);

        return true;
    }
}

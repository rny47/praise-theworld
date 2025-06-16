<?php

namespace TencentCore;

use Amazon\OssCore;
use Qcloud\Cos\Client;

/**
 * 腾讯云 COS 操作类
 */
class TencentCosCore
{
    /**
     * Secret Id
     *
     * @var string
     */
    protected $strSecretId  = "";

    /**
     * Secret Key
     *
     * @var string
     */
    protected $strSecretKey = "";

    /**
     * Region
     *
     * @var string
     */
    protected $strRegion = "";
    /**
     * Client
     *
     * @var Client
     */
    protected $Client = NULL;

    /**
     * init Cos Core
     *
     * @param string $strSecretId
     * @param string $strSecretKey
     * @param string $strRegion
     */
    public function __construct($strSecretId, $strSecretKey, $strRegion, $boolAccelerate = false)
    {
        $this->strSecretId = $strSecretId;
        $this->strSecretKey = $strSecretKey;
        $this->strRegion = $strRegion;

        $this->Client = new Client([
            'region' => $this->strRegion,
            'schema' => 'https',
            'credentials' => [
                'secretId'  => $this->strSecretId,
                'secretKey' => $this->strSecretKey,
            ],
            'allow_accelerate' => $boolAccelerate,
            'timeout' => 240, // 请求的超时时间（秒）
            'connect_timeout' => 240, // 连接超时时间（秒）
        ]);
    }

    /**
     * 获取存储桶权限
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function getBucketAccelerate($strBucket)
    {
        return $this->Client->getBucketAccelerate([
            'Bucket' => $strBucket,
        ]);
    }

    /**
     * 设置存储桶全球加速
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function setBucketAccelerate($strBucket, $boolStatus = true)
    {
        $strStatus = $boolStatus ? 'Enabled' : 'Suspended';
        return $this->Client->PutBucketAccelerate([
            'Bucket' => $strBucket,
            'Status' => $strStatus
        ]);
    }

    /**
     * 获取存储桶列表
     *
     * @return \GuzzleHttp\Command\Result
     */
    public function getBucketsList()
    {
        return $this->Client->listBuckets();
    }

    /**
     * 创建存储桶
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function createBucket($strBucket)
    {
        return $this->Client->createBucket([
            'Bucket' => $strBucket
        ]);
    }

    /**
     * 设置存储桶权限
     *
     * @param string $strBucket
     * @param string $strAcl
     * @return \GuzzleHttp\Command\Result
     */
    public function setBucketAcl($strBucket, $strAcl = 'public-read')
    {
        return $this->Client->putBucketAcl([
            'Bucket' =>  $strBucket,
            'ACL' => $strAcl,
        ]);
    }

    /**
     * 获取存储桶权限
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function getBucketAcl($strBucket)
    {
        return $this->Client->getBucketAcl([
            'Bucket' => $strBucket,
        ]);
    }

    /**
     * 上传文件
     *
     * @param string $strBucket
     * @param string $strFile
     * @param string $strKey
     * @return \GuzzleHttp\Command\Result
     */
    public function uploadFile($strBucket, $strFile, $strKey = NULL)
    {
        try {
            $File = fopen($strFile, 'rb');

            $Result = $this->Client->upload(
                $strBucket,
                $strKey,
                $File
            );
            if (is_resource($File)) {
                fclose($File);
            }

            return $Result;
        } catch (\Throwable $t) {
            throw $t;
        } finally {
            if (is_resource($File)) {
                fclose($File);
            }
        }
    }

    /**
     * 删除文件
     *
     * @param string $strBucket
     * @param string $strKey
     * @return \GuzzleHttp\Command\Result
     */
    public function delFile($strBucket, $strKey)
    {
        return $this->Client->deleteObject([
            'Bucket' => $strBucket,
            'Key' => $strKey
        ]);
    }

    /**
     * 删除存储桶
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function delBucket($strBucket)
    {
        return $this->Client->deleteBucket([
            'Bucket' => $strBucket
        ]);
    }

    /**
     * 查看存储桶所有文件
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function getBucketFileList($strBucket)
    {
        return $this->Client->listObjects([
            'Bucket' => $strBucket,
        ]);
    }


    /**
     * 查看存储桶文件下载地址
     *
     * @param string $strBucket
     * @return \GuzzleHttp\Command\Result
     */
    public function getObjectUrl($strBucket, $strKey)
    {
        return $this->Client->getObjectUrl(
            $strBucket,
            $strKey,
        );
    }
}

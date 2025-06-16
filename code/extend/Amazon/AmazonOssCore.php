<?php

namespace Amazon;

use Aws\Exception\MultipartUploadException;
use Aws\S3\S3Client;


/**
 * 亚马逊S3 COS 操作类
 */
class AmazonOssCore
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
     * @var S3Client
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

        $this->Client = new S3Client([
            'version' => 'latest',
            'region'  => $strRegion, // 您要使用的区域
            'credentials' => [
                'key'    => $strSecretId,
                'secret' => $strSecretKey,
            ],
            'use_accelerate_endpoint' => $boolAccelerate
        ]);
    }


    /**
     * 获取存储桶列表
     *
     * @return \Aws\Result
     */
    public function getBucketsList()
    {
        return  $this->Client->listBuckets();
    }

    /**
     * 创建存储桶
     *
     * @param string $strBucket
     * @return \Aws\Result
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
     * @return \Aws\Result
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
     * @return \Aws\Result
     */
    public function getBucketAcl($strBucket)
    {
        return  $this->Client->getBucketAcl([
            'Bucket' => $strBucket,
        ]);
    }

    /**
     * 获取存储桶全球加速
     *
     * @param string $strBucket
     * @return \Aws\Result
     */
    public function getBucketAccelerate($strBucket)
    {
        return  $this->Client->getBucketAccelerateConfiguration([
            'Bucket' => $strBucket,
        ]);
    }

    /**
     * 设置存储桶全球加速
     *
     * @param string $strBucket
     * @return \Aws\Result
     */
    public function setBucketAccelerate($strBucket, $boolStatus = true)
    {
        $strStatus = $boolStatus ? 'Enabled' : 'Suspended';
        return  $this->Client->putBucketAccelerateConfiguration([
            'Bucket' => $strBucket,
            'AccelerateConfiguration' => [
                'Status' => $strStatus,
            ]
        ]);
    }

    /**
     * 上传文件
     *
     * @param string $strBucket
     * @param string $strFile
     * @param string $strKey
     * @return \Aws\Result
     */
    public function uploadFile($strBucket, $strFile, $strKey = NULL)
    {
        $File = null;
        try {
            $File = fopen($strFile, 'rb');

            $Uploader = new \Aws\S3\MultipartUploader($this->Client, $File, [
                'bucket' => $strBucket,
                'key'    => $strKey,
                'concurrency' => 1  // 禁用并发上传
            ]);

            // $result = $Uploader->upload();
            $Promise = $Uploader->promise();
            $Result =  $Promise->wait();
        } catch (MultipartUploadException $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        } finally {
            if (is_resource($File)) {
                fclose($File);
            }
        }

        return $Result ?? null;
    }

    /**
     * 删除文件
     *
     * @param string $strBucket
     * @param string $strKey
     * @return \Aws\Result
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
     * @return \Aws\Result
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
     * @return \Aws\Result
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
     * @return \Aws\Result
     */
    public function getObjectUrl($strBucket, $strKey)
    {
        $strExpiry = '+30 minutes';

        $Cmd = $this->Client->getCommand('GetObject', [
            'Bucket' => $strBucket,
            'Key'    => $strKey
        ]);

        $Request = $this->Client->createPresignedRequest($Cmd, $strExpiry);

        return (string)$Request->getUri();
    }
}

<?php

namespace MinOSS;

use Amazon\AmazonOssCore;
use Aws\Exception\MultipartUploadException;
use Aws\S3\S3Client;


/**
 * MinIOCore 操作类
 */
class MinIOCore extends AmazonOssCore
{
    protected $strEndPoint = '';

    /**
     * init Cos Core
     *
     * @param string $strSecretId
     * @param string $strSecretKey
     * @param string $strRegion
     * @return S3Client
     */
    public function __construct($strSecretId, $strSecretKey, $strRegion, $strEndPoint)
    {
        $this->strSecretId = $strSecretId;
        $this->strSecretKey = $strSecretKey;
        $this->strRegion = $strRegion;
        $this->strEndPoint = $strEndPoint;

        $this->Client = new S3Client([
            'version'     => 'latest',
            'region'  => $this->strRegion, // 您要使用的区域
            'endpoint'    => $this->strEndPoint,
            'credentials' => [
                'key'    => $this->strSecretId,
                'secret' => $this->strSecretKey,
            ],
            'use_path_style_endpoint' => true,
            'http'       => [
                'verify' => false,
            ]
        ]);
    }
}

<?php

namespace Baota;

class BaotaCore
{
    /**
     * 接口密钥
     *
     * @var string
     */
    private $strBtKey = "";

    /**
     * 面板地址
     *
     * @var string
     */
    private $strBtPanel = "";

    /**
     * 如果希望多台面板，可以在实例化对象时，将面板地址与密钥传入
     *
     * @param string $strBtPanel
     * @param string $strBtKey
     */
    public function __construct($strBtPanel = null, $strBtKey = null)
    {
        if ($strBtPanel) $this->strBtPanel = $strBtPanel;
        if ($strBtKey) $this->strBtKey = $strBtKey;
    }

    /**
     * 获取站点列表
     *
     * @param array $arrData
     * @return array|null
     */
    public function getWebList($arrData = [])
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/data?action=getData';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        // $arrSendData['p'] = '1';
        // $arrSendData['limit'] = 10;
        // $arrSendData['type'] = 0;
        $arrSendData = array_merge($arrSendData, $arrData);
        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 添加站点
     *
     * @param array $arrData
     * @return array|null
     */
    public function addWebSite($arrData = [])
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=AddSite';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData = array_merge($arrSendData, $arrData);
        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 删除站点
     * @param int $intSiteId       [站点id]
     * @param string $strWebName   [站点名字]
     * @param int $intPath         [是否删除文件默认删除]
     * @return array|null
     */
    public function deleteWebSite($intSiteId, $strWebName, $intPath = 1)
    {
        $strUrl = $this->strBtPanel . '/site?action=DeleteSite';

        //准备POST数据
        $arrSendData = $this->getKeyData();     # 取签名
        $arrSendData['id']      = $intSiteId;   # 站点id
        $arrSendData['webname'] = $strWebName;  # 站点名称
        $arrSendData['path']    = $intPath;     # 删除目录

        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 重载Nginx配置
     *
     * @return array|null
     */
    public function reloadNginx()
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/system?action=ServiceAdmin';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['name'] = 'nginx';
        $arrSendData['type'] = 'reload';
        // $arrSendData['type'] = 0;

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 获取ssl证书
     *
     * @return array|null
     */
    public function getSSL()
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=GetSSL';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['siteName'] = 'test.com';

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 设置证书
     *
     * @param string $strSiteName
     * @param string $strKey
     * @param string $strCsr
     * @return array|null
     */
    public function setSSL($strSiteName, $strKey, $strCsr)
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=SetSSL';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['type'] = 1;
        $arrSendData['siteName'] = $strSiteName;
        $arrSendData['key'] = $strKey;
        $arrSendData['csr'] = $strCsr;

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }


    /**
     * 强制HTTPS
     *
     * @return array|null
     */
    public function httpToHttps()
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=HttpToHttps';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['siteName'] = 'test.com';

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 取消强制HTTPS
     *
     * @return array|null
     */
    public function closeToHttps()
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=CloseToHttps';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['siteName'] = 'test.com';

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }


    /**
     * 获取站点域名
     *
     * @param int $intSiteId
     * @return array
     */
    public function getDomain($intSiteId, $intLimit = 3)
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/data?action=getData';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['table'] = 'domain';  # 站点名称
        $arrSendData['list'] = True; # 站点ID
        $arrSendData['search'] = $intSiteId;

        try {
            //请求面板接口
            $strResult = $this->httpPostCookie($strUrl, $arrSendData);
            //解析JSON数据
            $arrResult = json_decode($strResult, true);
            if (!is_array($arrResult)) {
                throw new \Exception(sprintf('获取宝塔站点[%s]域名失败！', $intSiteId));
            }
        } catch (\Exception $e) {
            $intLimit--;
            if ($intLimit > 0) {
                return $this->getDomain($intSiteId, $intLimit);
            }
            throw $e;
        }

        return $arrResult;
    }

    /**
     * 添加域名
     *
     * @param string $strWebName
     * @param string $strSiteId
     * @param array $arrDomain
     * @return array
     */
    public function addDomain($strWebName, $strSiteId, $arrDomain, $intLimit = 3)
    {
        $strUrl = $this->strBtPanel . '/site?action=AddDomain';

        $arrSendData = $this->getKeyData();
        $arrSendData['webname'] = $strWebName;  # 站点名称
        $arrSendData['id'] = $strSiteId; # 站点ID
        $arrSendData['domain'] = implode(',', $arrDomain);

        try {
            $strResult = $this->httpPostCookie($strUrl, $arrSendData);

            $arrResult = json_decode($strResult, true);

            $strErr = '添加域名失败，宝塔未正常返回数据！';

            # 如果不是数组，且是空，则是系统级错误
            if (!is_array($arrResult) || empty($arrResult)) {
                throw new \Exception($strErr);
            }

            # 如果没有返回 domains 或者没有返回 status ，也是系统级错误
            if (!(!isset($arrResult['status']) || $arrResult['status'] != true)) {
                #这里宝塔只返回了status和msg
                throw new \Exception($strErr);
            }

            // if (!(isset($arrResult['domains']) || !isset($arrResult['status']))) {
            //     throw new \Exception($strErr);
            // }

            // if (isset($arrResult['status']) && $arrResult['status'] != true) {
            //     throw new \Exception($arrResult['msg'] ?? $strErr);
            // }

            // if (isset($arrResult['domains']) && empty($arrResult['domains'])) {
            //     throw new \Exception($strErr);
            // }

            // if (isset($arrResult['domains']) && !empty($arrResult['domains'])) {
            //     $boolResult = false;
            //     $strErr = '';
            //     foreach ($arrResult['domains'] as $arrDomainResult) {
            //         if ($arrDomainResult['status'] == false) {
            //             $strErr .= $arrDomainResult['name'] . ':' .  $arrDomainResult['msg'] . PHP_EOL;
            //         } else {
            //             $boolResult = true;
            //         }
            //     }
            //     if (!$boolResult) {
            //         throw new \Exception($strErr);
            //     }
            // }

            return [
                'status' => true,
                'msg' => '添加域名成功！'
            ];
        } catch (\Exception $e) {
            if (--$intLimit > 0) {
                return $this->addDomain($strWebName, $strSiteId, $arrDomain, $intLimit);
            }

            throw $e;
        }
    }


    /**
     * 获取重定向信息
     *
     * @param string $strWebName
     * @return array|null
     */
    public function getRedirectList($strWebName)
    {
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=GetRedirectList';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['sitename'] = $strWebName;  # 站点名称

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 删除重定向
     *
     * @param string $strSiteId
     * @param string $strRedirectId
     * @return array|null
     */
    public function delRedirectList($strSiteId, $strRedirectId)
    {
        if (is_array($strRedirectId)) {
            $strRedirectId = implode(',', $strRedirectId);
        }

        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=del_redirect_multiple';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['site_id'] = $strSiteId;  # 站点名称
        $arrSendData['redirectnames'] = $strRedirectId;  # 站点名称

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    public function addRedirectList($strWebName, $arrDomain, $strTargetDomain)
    {
        if (is_array($arrDomain)) {
            foreach ($arrDomain as &$strDomain) {
                $strDomain = '"' . $strDomain . '"';
            }
            $strDomainAll = '[' . implode(',', $arrDomain) . ']';
        } else {
            $strDomainAll = '["' .  $arrDomain . '"]';
        }
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=CreateRedirect';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['type'] = '1';
        $arrSendData['sitename'] = $strWebName;
        $arrSendData['holdpath'] = '1';
        $arrSendData['redirectname'] = time() . mt_rand(100, 999);
        $arrSendData['redirecttype'] = '301';
        $arrSendData['domainorpath'] = 'domain';
        $arrSendData['redirectpath'] = '';
        $arrSendData['redirectdomain'] = $strDomainAll;
        $arrSendData['tourl'] = $strTargetDomain;

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }


    /**
     * 删除域名
     *
     * @param int $intSiteId
     * @param array $arrDomainId
     * @return array|null
     */
    public function removeDomain($intSiteId, $arrDomainId)
    {
        if (is_string($arrDomainId)) {
            $arrDomainId = [$arrDomainId];
        }
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/site?action=delete_domain_multiple';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['id'] = $intSiteId;
        $arrSendData['domains_id'] = implode(',', $arrDomainId);

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 生成证书
     *
     * @param int $intSiteId
     * @param array $arrDomain
     * @return array|null
     */
    public function createSSL($intSiteId, $arrDomain)
    {
        if (is_array($arrDomain)) {
            foreach ($arrDomain as &$strDomain) {
                $strDomain = '"' . $strDomain . '"';
            }
            $strDomainAll = '[' . implode(',', $arrDomain) . ']';
        } else {
            $strDomainAll = '["' .  $arrDomain . '"]';
        }
        //拼接URL地址
        $strUrl = $this->strBtPanel . '/acme?action=apply_cert_api';

        //准备POST数据
        $arrSendData = $this->getKeyData();        //取签名
        $arrSendData['domains'] = $strDomainAll;  # 站点名称
        $arrSendData['auth_type'] = 'http'; # 站点ID
        $arrSendData['auth_to'] = $intSiteId;
        $arrSendData['auto_wildcard'] = 0;
        $arrSendData['id'] = $intSiteId;

        //请求面板接口
        $strResult = $this->httpPostCookie($strUrl, $arrSendData, 300);

        //解析JSON数据
        $arrResult = json_decode($strResult, true);
        return $arrResult;
    }

    /**
     * 构造带有签名的关联数组
     *
     * @return array
     */
    private function getKeyData()
    {
        $intNowTime = time();
        $arrSendData = [
            'request_token'    =>    md5($intNowTime . '' . md5($this->strBtKey)),
            'request_time'    =>    $intNowTime
        ];
        return $arrSendData;
    }


    /**
     * 发起POST请求
     * @param String $strUrl 目标网填，带http://
     * @param Array|String $arrResult 欲提交的数据
     * @param Int $intTimeOut 超时时间
     * @return string|bool
     */
    private function httpPostCookie($strUrl, $arrResult, $intTimeOut = 60)
    {
        //定义cookie保存位置
        $strCookieDir = storage_path() . '/btcookie/';
        if (!file_exists($strCookieDir)) {
            @mkdir($strCookieDir, 0777, true);
        }

        $strCookieFile = $strCookieDir . md5($this->strBtPanel) . '.cookie';
        if (!file_exists($strCookieFile)) {
            $Fp = fopen($strCookieFile, 'w+');
            fclose($Fp);
        }

        $Ch = curl_init();
        curl_setopt($Ch, CURLOPT_URL, $strUrl);
        curl_setopt($Ch, CURLOPT_TIMEOUT, $intTimeOut);
        curl_setopt($Ch, CURLOPT_POST, 1);
        curl_setopt($Ch, CURLOPT_POSTFIELDS, $arrResult);
        curl_setopt($Ch, CURLOPT_COOKIEJAR, $strCookieFile);
        curl_setopt($Ch, CURLOPT_COOKIEFILE, $strCookieFile);
        curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($Ch, CURLOPT_HEADER, 0);
        curl_setopt($Ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($Ch, CURLOPT_SSL_VERIFYPEER, false);
        $strResult = curl_exec($Ch);
        curl_close($Ch);
        return $strResult;
    }
}

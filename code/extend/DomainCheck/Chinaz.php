<?php

namespace DomainCheck;

use Exception;

class Chinaz
{
    public static function createDomainInfoPage($strDomain, $intLimit = 3)
    {
        $intTime = time();

        $strUrl = "https://whois.chinaz.com/" . $strDomain;

        $arrHeader = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: en',
            'Sec-Ch-Ua: "Google Chrome";v="113", "Chromium";v="113", "Not-A.Brand";v="24"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
        ];

        $Curl = curl_init();

        # 使用代理
        if (config('proxy.type') == 'xiongmao') {
            $strOrderno = config('proxy.orderno'); //自行更换
            $strSecret = config('proxy.secret'); ////自行更换

            #计算签名
            $strArgs = 'orderno=%s,secret=%s,timestamp=%s';
            $strArgs = sprintf($strArgs, $strOrderno, $strSecret, $intTime);
            $strSign = strtoupper(md5($strArgs));

            $strAuth = 'sign=%s&orderno=%s&timestamp=%s';
            $strAuth = sprintf($strAuth, $strSign, $strOrderno, $intTime);

            curl_setopt($Curl, CURLOPT_PROXY, config('proxy.xiongmao'));
            $arrHeader[] = "Proxy-Authorization:" . $strAuth;
        } else if (config('proxy.type') == 'kuai') {
            curl_setopt($Curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($Curl, CURLOPT_PROXY, config('proxy.kuai.kuaiproxy'));

            //隧道用户名密码
            $strKuaiUser   = config('proxy.kuai.kuaiusername');
            $strKuaiPassword   =  config('proxy.kuai.kuaipassword');
            curl_setopt($Curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($Curl, CURLOPT_PROXYUSERPWD, "{$strKuaiUser}:{$strKuaiPassword}");
        }

        curl_setopt($Curl, CURLOPT_HTTPHEADER, $arrHeader);
        curl_setopt($Curl, CURLOPT_USERAGENT, config('proxy.head_ua'));
        curl_setopt($Curl, CURLOPT_ENCODING, 'gzip, deflate, br');
        curl_setopt($Curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($Curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Curl, CURLOPT_URL, $strUrl);
        curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
        $strResult = curl_exec($Curl);
        curl_close($Curl);

        $strResult = (string)$strResult;
        $arrMatches = [];
        preg_match_all('/(\d{4})年(\d{1,2})月(\d{1,2})日/', $strResult, $arrMatches);

        $arrMatches = (array)$arrMatches;

        if (count($arrMatches) == 4 && count($arrMatches[0]) == 3) {
            $strDate = $arrMatches[0][2];
            $strDate = str_replace(['年', '月'], '-', $strDate);
            $strDate = str_replace('日', '', $strDate);
            return strtotime($strDate);
        } else {
            $intLimit--;
            if ($intLimit > 0) {
                return self::createDomainInfoPage($strDomain, $intLimit);
            }

            $strMsg = '站长工具返回异常数据，匹配到的域名是:%s';
            $strMsg = sprintf($strMsg, print_r($arrMatches, true));
            throw new Exception($strMsg);
        }
    }
}

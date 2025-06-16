<?php

namespace DomainCheck;



class AliyunMonitorUtil
{

    /**
     * @params $address 域名地址
     * @return null
     */
    public static function createOnceMonitor($address)
    {
        $data = array(
            'product' => 'metrics20180308', 'action' => 'BatchCreateOnceSiteMonitor',
            'params' => '{"TaskList.1.TaskType":"1","TaskList.1.TaskName":"b1454a88_754f2ce187d091d4a6",
            "TaskList.1.Address":"' . $address . '","TaskList.1.Interval":0,"TaskList.1.IspCity":[{"isp":"232","city":"618"},{"isp":"5","city":"641"},{"isp":"132","city":"524"},{"isp":"232","city":"641"},{"isp":"5","city":"395"},{"isp":"132","city":"395"},{"isp":"5","city":"379"},{"isp":"132","city":"641"},{"isp":"232","city":"395"},{"isp":"5","city":"304"},{"isp":"232","city":"247"},{"isp":"232","city":"345"},{"isp":"232","city":"777"},{"isp":"132","city":"345"},{"isp":"132","city":"777"},{"isp":"5","city":"345"},{"isp":"132","city":"532"},{"isp":"5","city":"777"},{"isp":"132","city":"304"},{"isp":"5","city":"532"},{"isp":"232","city":"304"},{"isp":"5","city":"738"},{"isp":"232","city":"676"},{"isp":"132","city":"738"},{"isp":"5","city":"676"},{"isp":"132","city":"676"},{"isp":"232","city":"127"},{"isp":"232","city":"558"},{"isp":"232","city":"250"},{"isp":"132","city":"250"},{"isp":"132","city":"127"},{"isp":"5","city":"127"},{"isp":"132","city":"558"},{"isp":"5","city":"558"},{"isp":"232","city":"738"},{"isp":"5","city":"250"},{"isp":"5","city":"252"},{"isp":"132","city":"322"},{"isp":"132","city":"252"},{"isp":"232","city":"252"},{"isp":"132","city":"357"},{"isp":"232","city":"226"},{"isp":"5","city":"595"},{"isp":"132","city":"595"},{"isp":"232","city":"690"},{"isp":"5","city":"357"},{"isp":"5","city":"226"},{"isp":"132","city":"515"},{"isp":"132","city":"414"},{"isp":"132","city":"572"},{"isp":"232","city":"718"},{"isp":"132","city":"226"},{"isp":"5","city":"515"},{"isp":"5","city":"546"},{"isp":"5","city":"718"},{"isp":"5","city":"690"},{"isp":"232","city":"790"},{"isp":"232","city":"572"},{"isp":"232","city":"595"},{"isp":"232","city":"357"},{"isp":"232","city":"491"},{"isp":"132","city":"491"},{"isp":"132","city":"619"},{"isp":"132","city":"421"},{"isp":"5","city":"619"},{"isp":"232","city":"421"},{"isp":"232","city":"619"},{"isp":"5","city":"1156"},{"isp":"5","city":"421"},{"isp":"232","city":"765"},{"isp":"132","city":"103"},{"isp":"5","city":"301"},{"isp":"232","city":"103"},{"isp":"232","city":"268"},{"isp":"132","city":"301"},{"isp":"132","city":"4"},{"isp":"5","city":"268"},{"isp":"132","city":"765"},{"isp":"5","city":"765"},{"isp":"5","city":"103"},{"isp":"232","city":"4"},{"isp":"5","city":"4"},{"isp":"232","city":"111"},{"isp":"5","city":"598"},{"isp":"132","city":"311"},{"isp":"232","city":"50"},{"isp":"5","city":"111"},{"isp":"232","city":"311"},{"isp":"132","city":"50"},{"isp":"132","city":"239"},{"isp":"5","city":"50"},{"isp":"5","city":"239"},{"isp":"232","city":"598"},{"isp":"132","city":"598"},{"isp":"5","city":"311"}],"TaskList.1.Options":{"time_out":30000,"enable_operator_dns":true,"count":5,"http_method":"get"}}'
        );


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://boce.aliyun.com/data/api.json?action=BatchCreateOnceSiteMonitor',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'x-xsrf-token: d3c5c5e6-5404-4de6-87a6-280f79da0b9b',
                'Cookie: XSRF-TOKEN=d3c5c5e6-5404-4de6-87a6-280f79da0b9b; arms_uid=100b172c-0318-4ab9-8284-d95429414064; cna=6HagG1GE4hsCAbcPsJ64vkuH; currentRegionId=cn-hangzhou; login_aliyunid_csrf=_csrf_tk_1908182304171821; tfstk=cFw1I0b4OFY1mf6DIVsUdXnCGGpAYOiBVm1sSl_ac7gl39KpC49fiLJrP8_8iEHqDD1..; l=fBTNHstINYhzEP1ABO5CPurza779oaRbosPzaNbMiIEGI6RbOFn6Vz8xMudM6_nR5HG5-TfxmHKyc-fcNdEwSjULRE_09tE7CaDMvD998bpU-L5..; isg=BBISCEs9outJyt6U4H2MG4NUY9j0Ixa91JV6k9xRukc479Dp1bBzzf5JX0tTn45V'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * @params $taskId
     * @params $startTime 开始时间 毫秒时间戳
     * @params $endTime 结束时间 毫秒时间戳
     * @return bool|string
     */
    public static function getTaskInfo($taskId, $startTime, $endTime)
    {
        $data = array(
            'product' => 'metrics20190101',
            'action' => 'DescribeMetricEventList',
            'params' => '{"Dimensions":[{"taskId":"' . $taskId . '"}],"Namespace":"acs_networkmonitor","EventName":"ProbeLog","Length":5000,
                "EndTime":' . $endTime . ',"StartTime":' . $startTime . '}'
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://boce.aliyun.com/data/api.json?action=DescribeMetricEventList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'x-xsrf-token: d3c5c5e6-5404-4de6-87a6-280f79da0b9b',
                'Cookie: XSRF-TOKEN=d3c5c5e6-5404-4de6-87a6-280f79da0b9b; arms_uid=100b172c-0318-4ab9-8284-d95429414064; cna=6HagG1GE4hsCAbcPsJ64vkuH; currentRegionId=cn-hangzhou; login_aliyunid_csrf=_csrf_tk_1908182304171821; tfstk=cRG1IPtqdGj6oROcslTEOJUBhFCw8OO5Pr9_jVtZlYZkg9LBquCfig5zVbt-niF4ky1..; l=fBTNHstINYhzEE26B91QEurza77TqSAb81r0aNbMiIEzjOfeq7Ytf3aykjEJsQsjYNtINwXlMYtp4nwci_9tZFFU8yYfoWBYVR_oIHnDBelJLyvP.; isg=BIWFO0BcTULP3mkdGzxTwljFlMG_QjnUH0RtWoeqCr5DHsaQXpCxpbu4KELoXlGM'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /**
     * 代理请求方式创建阿里云任务
     *
     * @param string $strDomain
     * @param integer $intLimit
     * @return \Excetpint | array
     */
    public static function createOnceMonitorProxy($strDomain, $intLimit = 3)
    {
        $intTime = time();

        $strUrl = "https://boce.aliyun.com/data/api.json?action=BatchCreateOnceSiteMonitor";

        $strData = "csrf_token=mock-sec-token&_csrf=mock-sec-token&sec_token=mock-sec-token&product=metrics20180308&action=BatchCreateOnceSiteMonitor&region=cn-hangzhou&params=%7B%22TaskList.1.TaskType%22%3A%221%22%2C%22TaskList.1.TaskName%22%3A%22b1284d55_2de1f3318814803c97%22%2C%22TaskList.1.Address%22%3A%22" . $strDomain . "%22%2C%22TaskList.1.Interval%22%3A0%2C%22TaskList.1.IspCity%22%3A%5B%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22618%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22641%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22524%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22641%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22395%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22395%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22379%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22641%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22395%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22304%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22247%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22345%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22777%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22345%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22345%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22532%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22777%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22304%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22532%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22304%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22738%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22676%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22738%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22676%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22676%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22127%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22558%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22250%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22250%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22127%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22127%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22558%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22558%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22738%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22250%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22252%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22322%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22252%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22252%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22357%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22226%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22595%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22595%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22690%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22357%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22226%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22515%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22414%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22572%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22718%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22226%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22515%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22546%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22718%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22690%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22790%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22572%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22595%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22357%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22491%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22491%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22619%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22421%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22619%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22421%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22619%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%221156%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22421%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22765%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22103%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22301%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22103%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22268%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22301%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%224%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22268%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22301%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22765%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22765%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22103%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%224%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%224%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22111%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22598%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22311%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%2250%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22111%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22311%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%2250%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22239%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%2250%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22239%22%7D%2C%7B%22isp%22%3A%22232%22%2C%22city%22%3A%22598%22%7D%2C%7B%22isp%22%3A%22132%22%2C%22city%22%3A%22598%22%7D%2C%7B%22isp%22%3A%225%22%2C%22city%22%3A%22311%22%7D%5D%2C%22TaskList.1.Options%22%3A%7B%22time_out%22%3A30000%2C%22enable_operator_dns%22%3Atrue%2C%22count%22%3A5%2C%22http_method%22%3A%22get%22%7D%7D&umid=mock-umid&collina=mock-collina-ua";

        $arrHeader = [
            "Connection: keep-alive",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Upgrade-Insecure-Requests: 1",
            "DNT:1",
            "Accept-Language: zh-CN,zh;q=0.8,en-GB;q=0.6,en;q=0.4,en-US;q=0.2",
            'authority: boce.aliyun.com',
            'accept: */*',
            'accept-language: zh-CN,zh;q=0.9',
            'bx-v: 2.5.0',
            'cache-control: no-cache',
            'content-type: application/x-www-form-urlencoded',
            'cookie: cna=sLQRHBvxVHoCARsm5Y58uwhd; t=45ac9996683ed2a59e8a1052231d04c6; aliyun_site=CN; XSRF-TOKEN=89662ecb-0677-44a1-bc97-858f4b06aba2; currentRegionId=cn-hangzhou; _samesite_flag_=true; cookie2=1ce5695aeb73a6437b305744a6950b22; _tb_token_=3197ee575533e; arms_uid=fc971fb7-6852-4318-9bab-c464fc5a2049; aliyun_choice=CN; login_aliyunid_csrf=_csrf_tk_1121282069666598; l=fBSf8gJPLaOJwYrXBO5Cnurza779aCAflZVzaNbMiIEGa6tNIF6gfNC_KjbWxdtxgTfYqE-ygJ-c1d3H5zUU5E_09tE7CaDMv8p6-bpU-L5..; tfstk=cE3OBQsOJBC9FWp08PKhg5cBZ7sAa-_U3nwlkVntrp-kzLbcusxqr4e3BsN7URdd.; isg=BMzMjcZqxEB-odbLSqtvnWm0nSz-BXCvzTVUFSaJiXcasWa7QRcAPlBHULmJ16gH',
            'origin: https://boce.aliyun.com',
            'pragma: no-cache',
            'referer: https://boce.aliyun.com/detect/http',
            'sec-ch-ua: "Google Chrome";v="113", "Chromium";v="113", "Not-A.Brand";v="24"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "macOS"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-origin',
            'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
            'x-xsrf-token: 89662ecb-0677-44a1-bc97-858f4b06aba2',
            'Cookie:x-wl-uid=13+U2muoAsqb+cGICuLwsZxdB0zh3Cxftc3w0L1osYFaHBsx69LBKo+Ye1VlRE6mQYAJdfOz/pCU=; session-token=VCkyOjvA47ITaveT83S7oYA8fykTsO+1DRP99i+7pYoZS6t1O5Rc2grgfX7v1MQsvcTcd04UoSNd2LxjUFw7KoAuVTbq8i1U4CuJqn8xGyP71O7MeEOXbGOov6s3dgcaDBObgk7TEq6l+9LulY9sk/ddoh5sJXeZCJCDdv5ui9Dx6FDNQQoGI6jS/i/mWQLH5jUYgCfwRZgWLb/LYt4RzXrAUaldRJPhfwK0fVNuCZt1ZpexTddfMxnzeq5+gmRB; csm-hit=s-673XVDH32ASPKJC03C10|1499052510486; ubid-main=134-1565384-9955147; session-id-time=2082787201l; session-id=144-7849927-7600125',
        ];

        $Ch = curl_init();

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

            curl_setopt($Ch, CURLOPT_PROXY, config('proxy.xiongmao'));
            $arrHeader[] =     "Proxy-Authorization:" . $strAuth;
        } else if (config('proxy.type') == 'kuai') {
            curl_setopt($Ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($Ch, CURLOPT_PROXY, config('proxy.kuai.kuaiproxy'));

            //隧道用户名密码
            $strKuaiUser   = config('proxy.kuai.kuaiusername');
            $strKuaiPassword   =  config('proxy.kuai.kuaipassword');
            curl_setopt($Ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($Ch, CURLOPT_PROXYUSERPWD, "{$strKuaiUser}:{$strKuaiPassword}");
        }

        curl_setopt($Ch, CURLOPT_HTTPHEADER, $arrHeader);
        curl_setopt($Ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11');
        curl_setopt($Ch, CURLOPT_REFERER, "https://2022.ip138.com");
        curl_setopt($Ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($Ch, CURLOPT_POSTFIELDS, $strData);
        curl_setopt($Ch, CURLOPT_ENCODING, 'gzip, deflate, sdch');
        curl_setopt($Ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($Ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($Ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Ch, CURLOPT_URL, $strUrl);
        curl_setopt($Ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1);
        $strResponse = curl_exec($Ch);
        curl_close($Ch);

        $strResponse = (string)$strResponse;
        $arrTask = json_decode($strResponse, true);

        if (
            !is_array($arrTask)
            || !isset($arrTask['code'])
            || $arrTask['code'] != 200
            || !isset($arrTask['data'])
            || !isset($arrTask['data']['Data'])
            || !is_array($arrTask['data']['Data'])
            || empty($arrTask['data']['Data'])
        ) {
            if (--$intLimit > 0) {
                return self::createOnceMonitorProxy($strDomain, $intLimit);
            }
            $strMsg = sprintf("创建拨测检测域名[%s]状态任务失败！原因：%s", $strDomain, '阿里云未正常返回结果！');
            throw new \Exception($strMsg);
        }

        return $arrTask;
    }



    /**
     * @params string $taskId
     * @params string $startTime 开始时间 毫秒时间戳
     * @params string $endTime 结束时间 毫秒时间戳
     * @return \Excetpint | array
     */
    public static function getTaskInfoProxy($strTaskId, $intStartTime, $intEndTime, $intLimit = 3)
    {
        $intTime = time();

        $strUrl = "https://boce.aliyun.com/data/api.json?action=DescribeMetricEventList";

        $arrData = [
            'product' => 'metrics20190101',
            'action' => 'DescribeMetricEventList',
            'params' => '{"Dimensions":[{"taskId":"' . $strTaskId . '"}],"Namespace":"acs_networkmonitor","EventName":"ProbeLog","Length":5000,
                "EndTime":' . $intEndTime . ',"StartTime":' . $intStartTime . '}'
        ];

        $arrHeader =  [
            "Connection: keep-alive",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Upgrade-Insecure-Requests: 1",
            "DNT:1",
            "Accept-Language: zh-CN,zh;q=0.8,en-GB;q=0.6,en;q=0.4,en-US;q=0.2",
            'Cookie:x-wl-uid=13+U2muoAsqb+cGICuLwsZxdB0zh3Cxftc3w0L1osYFaHBsx69LBKo+Ye1VlRE6mQYAJdfOz/pCU=; session-token=VCkyOjvA47ITaveT83S7oYA8fykTsO+1DRP99i+7pYoZS6t1O5Rc2grgfX7v1MQsvcTcd04UoSNd2LxjUFw7KoAuVTbq8i1U4CuJqn8xGyP71O7MeEOXbGOov6s3dgcaDBObgk7TEq6l+9LulY9sk/ddoh5sJXeZCJCDdv5ui9Dx6FDNQQoGI6jS/i/mWQLH5jUYgCfwRZgWLb/LYt4RzXrAUaldRJPhfwK0fVNuCZt1ZpexTddfMxnzeq5+gmRB; csm-hit=s-673XVDH32ASPKJC03C10|1499052510486; ubid-main=134-1565384-9955147; session-id-time=2082787201l; session-id=144-7849927-7600125',
            'x-xsrf-token: d3c5c5e6-5404-4de6-87a6-280f79da0b9b',
            'Cookie: XSRF-TOKEN=d3c5c5e6-5404-4de6-87a6-280f79da0b9b; arms_uid=100b172c-0318-4ab9-8284-d95429414064; cna=6HagG1GE4hsCAbcPsJ64vkuH; currentRegionId=cn-hangzhou; login_aliyunid_csrf=_csrf_tk_1908182304171821; tfstk=cRG1IPtqdGj6oROcslTEOJUBhFCw8OO5Pr9_jVtZlYZkg9LBquCfig5zVbt-niF4ky1..; l=fBTNHstINYhzEE26B91QEurza77TqSAb81r0aNbMiIEzjOfeq7Ytf3aykjEJsQsjYNtINwXlMYtp4nwci_9tZFFU8yYfoWBYVR_oIHnDBelJLyvP.; isg=BIWFO0BcTULP3mkdGzxTwljFlMG_QjnUH0RtWoeqCr5DHsaQXpCxpbu4KELoXlGM'
        ];

        $Ch = curl_init();
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

            curl_setopt($Ch, CURLOPT_PROXY, config('proxy.xiongmao'));
            $arrHeader[] = "Proxy-Authorization:" . $strAuth;
        } else if (config('proxy.type') == 'kuai') {
            curl_setopt($Ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($Ch, CURLOPT_PROXY, config('proxy.kuai.kuaiproxy'));

            //隧道用户名密码
            $strKuaiUser   = config('proxy.kuai.kuaiusername');
            $strKuaiPassword   =  config('proxy.kuai.kuaipassword');
            curl_setopt($Ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($Ch, CURLOPT_PROXYUSERPWD, "{$strKuaiUser}:{$strKuaiPassword}");
        }


        curl_setopt($Ch, CURLOPT_HTTPHEADER, $arrHeader);

        curl_setopt($Ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11');
        curl_setopt($Ch, CURLOPT_REFERER, "https://2022.ip138.com");
        curl_setopt($Ch, CURLOPT_ENCODING, "gzip, deflate, sdch");
        curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($Ch, CURLOPT_POSTFIELDS, $arrData);
        curl_setopt($Ch, CURLOPT_URL, $strUrl);
        curl_setopt($Ch, CURLOPT_SSL_VERIFYPEER, FALSE);


        curl_setopt($Ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($Ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1);
        $strResponse = curl_exec($Ch);
        curl_close($Ch);

        $strResponse = (string)$strResponse;
        $arrTaskResult = json_decode($strResponse, true);

        if (
            !is_array($arrTaskResult)
            || !isset($arrTaskResult['code'])
            || $arrTaskResult['code'] != 200
            || !isset($arrTaskResult['data'])
            || !isset($arrTaskResult['data']['Datapoints'])
            || empty($arrTaskResult['data']['Datapoints'])
        ) {
            if (--$intLimit > 0) {
                return self::getTaskInfoProxy($strTaskId, $intStartTime, $intEndTime, $intLimit);
            }
            $strMsg = sprintf("返回异常：", $strResponse);

            throw new \Exception($strMsg);
        }

        return $arrTaskResult;
    }
}

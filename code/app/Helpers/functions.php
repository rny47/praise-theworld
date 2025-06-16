<?php


/**
 * 打印异常
 *
 * @param \Throwable  $e
 * @param mixed $mixedData
 * @return string
 */
function debugErr(\Throwable  $e, string $strTitle, $mixedData = NULL)
{
    $strErr = date('Y-m-d H:i:s') . PHP_EOL .
        '标题: ' . $strTitle . PHP_EOL .
        '文件: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL .
        '错误: ' . $e->getMessage() . PHP_EOL .
        '堆栈: ' . PHP_EOL . $e->getTraceAsString() . PHP_EOL .
        '数据: ' . var_export($mixedData, true) . PHP_EOL . PHP_EOL;

    echo $strErr;
    return $strErr;
}


/**
 * 将字符串 时:分:秒  转换成秒
 *
 * @param string $strTime
 * @return int
 */
function strTimeToSeconds($strTime)
{
    $arrTime = explode(":", $strTime);

    if (!is_array($arrTime) || count($arrTime) < 3) {
        throw new \Exception('字符串时间格式必须是: 时:分:秒');
    }

    $intSeconds = $arrTime[0] * 3600 + $arrTime[1] * 60 + $arrTime[2];
    return $intSeconds;
}

/**
 * 将字符串 时:分:秒  转换成秒
 *
 * @param int $strTime
 * @return string
 */
function secondsToStrTime($intPtLimit)
{
    $intHours = floor($intPtLimit / 3600);
    $intMinutes = floor(($intPtLimit % 3600) / 60);
    $intPtLimit = $intPtLimit % 60;
    return sprintf("%02d:%02d:%02d", $intHours, $intMinutes, $intPtLimit);
}

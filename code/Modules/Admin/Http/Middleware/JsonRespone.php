<?php

namespace Modules\Admin\Http\Middleware;

use Illuminate\Http\JsonResponse as SystemJsonResponse;

class JsonRespone
{
    /**
     *  Format Data to create SystemJsonResponse
     *
     * @param  string $strCode
     * @param  string $strMsg
     * @param  array $arrData
     * @param  array $arrSprint
     * @return SystemJsonResponse
     */
    public static function getResourceByCode($strCode = "000", $strMsg = "", $arrData = [], $arrSprint = []): SystemJsonResponse
    {
        $arrCode = config("error.code");

        if (!key_exists($strCode, $arrCode)) {
            $strCode = '999';
        }
        $strMsg = empty($strMsg) ? $arrCode[$strCode] : $strMsg;
        if (!empty($arrSprint)) {
            $strMsg = vsprintf($strMsg, $arrSprint);
        }

        return response()->json([
            'data' => $arrData,
            'message' => $strMsg,
            'code' =>  $strCode,
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     *  Format Data to create SystemJsonResponse
     * @param  array $arrData
     * @param  array $arrSprint
     * @param  string $strCode
     * @param  string $strMsg
     * @return SystemJsonResponse
     */
    public static function getResourceSuccess($arrData = [], $arrSprint = [], $strCode = "000", $strMsg = ""): SystemJsonResponse
    {
        $arrCode = config("error.code");
        if (!key_exists($strCode, $arrCode)) {
            $strCode = '999';
        }
        $strMsg = empty($strMsg) ? $arrCode[$strCode] : $strMsg;
        if (!empty($arrSprint)) {
            $strMsg = vsprintf($strMsg, $arrSprint);
        }

        return response()->json([
            'data' => $arrData,
            'message' => $strMsg,
            'code' =>  $strCode,
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     *  Format Data to create SystemJsonResponse
     *
     * @param  string $strCode
     * @param  string $strMsg
     * @param  array $arrData
     * @param  array $arrSprint
     * @return SystemJsonResponse
     */
    public static function getResourceByValidator(\Illuminate\Validation\Validator $Validator)
    {
        return self::getResourceByCode('997', $Validator->errors()->first());
    }
}

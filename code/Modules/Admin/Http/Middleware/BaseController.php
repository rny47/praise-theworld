<?php

namespace Modules\Admin\Http\Middleware;

use Illuminate\Http\JsonResponse as SystemJsonRespone;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    protected function success($mixedData = [], $strMsg = 'Success', $strCode = '000'): SystemJsonRespone
    {
        return JsonRespone::getResourceByCode($strCode, $strMsg, $mixedData);
    }

    protected function error($strCode = "999", $strMsg = "", $mixedData = [], $arrSprint = []): SystemJsonRespone
    {
        return JsonRespone::getResourceByCode($strCode, $strMsg, $mixedData, $arrSprint);
    }

    protected function errorValidator($Validator): SystemJsonRespone
    {
        return JsonRespone::getResourceByValidator($Validator);
    }
}

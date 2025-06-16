<?php

namespace Modules\Admin\Http\Middleware;

use Illuminate\Foundation\Exceptions\Handler as SystemExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler extends SystemExceptionHandler
{
    private function setCors($Request, Throwable $Throwable)
    {
        $Response = parent::render($Request, $Throwable);
        $Response->headers->set('Access-Control-Allow-Origin',  $Request->header('Origin', '*'));
        $Response->headers->set('Access-Control-Allow-Methods', '*');
        $Response->headers->set('Access-Control-Allow-Headers', 'DNT,range,token,request,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization,admin-token');
        $Response->headers->set('Access-Control-Allow-Credentials', 'true');
        $Response->headers->set('Access-Control-Max-Age', 7200);
        $Response->headers->set('Content-Type', 'application/json');
    }

    public function render($Request, Throwable $Throwable)
    {
        $this->setCors($Request, $Throwable);

        if ($Throwable instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            $strCode = $Throwable->getCode();
            $strMsg =  $Throwable->getResponse()->getContent();
            $mixedData = [];
        } elseif ($Throwable instanceof \Illuminate\Validation\ValidationException) {
            $strCode = '997';
            $strMsg = $Throwable->validator->errors();
            $mixedData = [];
        } elseif ($Throwable instanceof NotFoundHttpException) {
            $strCode = '998';
            $strMsg = '';
            $mixedData = [];
        } elseif ($Throwable instanceof HttpException) {
            $strCode = $Throwable->getCode();
            $strMsg =  $Throwable->getMessage();
            $mixedData = [];
        } else {
            $strCode = '999';
            $strMsg =  $Throwable->getMessage();
            $mixedData = [];
        }

        return JsonRespone::getResourceByCode($strCode, $strMsg, $mixedData);
    }
}

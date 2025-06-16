<?php

namespace Modules\Admin\Http\Middleware;

class HttpException extends \Exception
{
    public function __construct($strCode = '999', $strMsg = '')
    {
        return parent::__construct($strMsg, $strCode);
    }
}

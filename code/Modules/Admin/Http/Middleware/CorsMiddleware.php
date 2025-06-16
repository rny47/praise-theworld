<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Response
     *
     * @var \Illuminate\Http\Response
     */
    private $Response = NULL;

    /**
     * Response
     *
     * @var \Illuminate\Http\Request
     */
    private $Request = NULL;

    /**
     * Set response cors header
     *
     * @return void
     */
    public function setCors()
    {
        $this->Response->headers->set('Access-Control-Allow-Origin',  $this->Request->header('Origin', '*'));
        $this->Response->headers->set('Access-Control-Allow-Methods', '*');
        $this->Response->headers->set('Access-Control-Allow-Headers', 'DNT,range,token,request,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization,admin-token');
        $this->Response->headers->set('Access-Control-Allow-Credentials', 'true');
        $this->Response->headers->set('Access-Control-Max-Age', 7200);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $Request
     * @param  \Closure  $Next
     * @return mixed
     */
    public function handle(Request $Request, Closure $Next)
    {
        $this->Request = $Request;
        if ($Request->isMethod('OPTIONS')) {
            $this->Response = response(NULL, 204);
        } else {
            $this->Response = $Next($Request);
        }

        $this->setCors();

        return $this->Response;
    }
}

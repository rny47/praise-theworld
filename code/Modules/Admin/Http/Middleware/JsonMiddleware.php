<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $Request
     * @param  \Closure  $Next
     * @return mixed
     */
    public function handle(Request $Request, Closure $Next)
    {
        $Response = $Next($Request);
        $Response->headers->set('Content-Type', 'application/json');
        return $Response;
    }
}

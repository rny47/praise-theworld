<?php

namespace Modules\Admin\Http\Middleware;

use App\Models\AdminUsersModel;
use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
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
        $strToken = $Request->header("admin-token");

        $AdminUsers = AdminUsersModel::getCacheAdminUserByToken($strToken);

        // $AdminUsers = AdminUsersModel::where('au_id',1)->first();

        if (empty($AdminUsers)) {
            throw new HttpException('991');
        }

        $strRouteName = $Request->route()->getName();

        if ($AdminUsers->checkAuth($strRouteName) === false) {
            throw new HttpException('992');
        }

        $Response = $Next($Request);

        return $Response;
    }
}

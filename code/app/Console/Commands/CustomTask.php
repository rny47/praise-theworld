<?php

namespace App\Console\Commands;

use App\Task\Core\Core;
use Illuminate\Console\Command;

class CustomTask extends Command
{
    protected $signature = 'task {action : start|stop|status|restart} {--d : 是否后台启动}  {--f : 忽略ROOT用户检测强制启动}';

    protected $arrArgumentRule = [
        'action' => [
            'start', 'stop', 'status', 'restart',
        ]
    ];

    protected $description = 'Swoole 微服务';

    protected $arrReflection = [
        Core::class,
    ];

    public function handle()
    {
        if (!$this->checkUser()) {
            return false;
        }

        if (!$this->checkArguments()) {
            return false;
        }

        $arrArgs = array_merge($this->arguments(), $this->options());

        $strAction = $this->argument('action');

        $this->{$strAction}($arrArgs);
    }

    protected function checkArguments()
    {
        $strAction = $this->argument('action');

        if (!in_array($strAction, $this->arrArgumentRule['action'])) {
            $strError = sprintf("Sorry ,Action can only be a  [%s]!", implode('|', $this->arrArgumentRule['action']));
            $this->error($strError);
            return false;
        }

        return true;
    }

    protected function checkUser()
    {
        $strAction = $this->argument('action');

        if (!in_array($strAction, ['start', 'restart',])) {
            return true;
        }

        $strForcibly = $this->option('f');
        if ($strForcibly) {
            return true;
        }

        $intUid = posix_geteuid();
        $strUser = posix_getpwuid($intUid)['name'];

        if ($strUser == 'root') {
            $strError = sprintf("Sorry , Can't start service by [%s] user!,\n but you can add option '--f' forcibly start on you debug ,\n Do not use in production environment   !!!!!!!!!", $strUser);
            $this->error($strError);
            return false;
        }

        if ($strUser != 'www') {
            $this->error("Sorry , Can't start service by [{$strUser}] user!, please use [www] user !");
            return false;
        }

        return true;
    }

    public function __call($strAction, $arrArguments)
    {
        foreach ($this->arrReflection as $strClass) {
            if (method_exists($strClass, $strAction)) {
                $Object = new $strClass($arrArguments, $this);
                return call_user_func([$Object, $strAction]);
            }
        }
        throw new \InvalidArgumentException("Action [{$strAction}] no method available! ");
    }
}

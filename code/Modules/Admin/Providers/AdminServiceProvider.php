<?php

namespace Modules\Admin\Providers;

use App\Models\AdminUsersModel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler as SystemExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Modules\Admin\Http\Middleware\ExceptionHandler;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Admin';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'admin';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isCurrentModuleRequest()) {
            $this->app->register(RouteServiceProvider::class);
            $this->app->singleton(SystemExceptionHandler::class, ExceptionHandler::class);
            $Kernel = $this->app->make(Kernel::class);
            $Kernel->pushMiddleware(\Modules\Admin\Http\Middleware\CorsMiddleware::class);
            $Kernel->pushMiddleware(\Modules\Admin\Http\Middleware\JsonMiddleware::class);

            $this->app->bind(AdminUsersModel::class, function () {
                $strToken = request()->header("admin-token");
                return AdminUsersModel::getCacheAdminUserByToken($strToken);
            });
        }
    }

    /**
     * Check url module is current module.
     *
     * @return boolean
     */
    protected function isCurrentModuleRequest(): bool
    {
        return request()->is($this->moduleNameLower, $this->moduleNameLower . '/*');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );

        $this->publishes([
            module_path($this->moduleName, 'Config/error.php') => config_path('error.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/error.php'),
            'error'
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

<?php

namespace Fligno\FlignoToolkit;

use Fligno\FlignoToolkit\Console\Commands\RequirePackageCommand;
use Fligno\FlignoToolkit\Console\Commands\ListGroupsCommand;
use Fligno\FlignoToolkit\Console\Commands\ListPackagesCommand;
use Fligno\FlignoToolkit\Console\Commands\ShowCurrentUserCommand;
use Illuminate\Support\ServiceProvider;

class FlignoToolkitServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    public array $commands = [
        ListGroupsCommand::class,
        ListPackagesCommand::class,
        ShowCurrentUserCommand::class,
        RequirePackageCommand::class,
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fligno');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'fligno');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fligno-toolkit.php', 'fligno-toolkit');

        // Register the service the package provides.
        $this->app->singleton('fligno-toolkit', function ($app) {
            return new FlignoToolkit;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['fligno-toolkit'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/fligno-toolkit.php' => config_path('fligno-toolkit.php'),
        ], 'fligno-toolkit.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/fligno'),
        ], 'fligno-toolkit.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/fligno'),
        ], 'fligno-toolkit.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/fligno'),
        ], 'fligno-toolkit.views');*/

        // Registering package commands.
         $this->commands($this->commands);
    }
}

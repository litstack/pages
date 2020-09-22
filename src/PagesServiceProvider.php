<?php

namespace Litstack\Pages;

use Illuminate\Support\ServiceProvider;

class PagesServiceProvider extends ServiceProvider
{
    /**
     * Commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        Commands\PagesCommand::class,
        Commands\PagesControllerCommand::class,
    ];

    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublishes();

        $this->registerCommands();

        $this->registerPagesRoutes();

        $this->app->register(PagesRouteServiceProvider::class);
    }

    /**
     * Register litstack pages routes.
     *
     * @return void
     */
    protected function registerPagesRoutes()
    {
        $this->app->singleton('lit.pages.routes', function ($app) {
            return new PagesRoutes($app);
        });
    }

    /**
     * Register publishes.
     *
     * @return void
     */
    protected function registerPublishes()
    {
        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands($this->commands);
    }
}

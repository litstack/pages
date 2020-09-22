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
        Commands\Litstack\PagesCommand::class,
        Commands\Litstack\PagesControllerCommand::class,
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

        $this->registerLitstack\PagesRoutes();

        $this->app->register(Litstack\PagesRouteServiceProvider::class);
    }

    /**
     * Register fjord pages routes.
     *
     * @return void
     */
    protected function registerLitstack\PagesRoutes()
    {
        $this->app->singleton('fjord.pages.routes', function ($app) {
            return new Litstack\PagesRoutes($app);
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

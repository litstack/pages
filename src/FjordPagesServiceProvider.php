<?php

namespace FjordPages;

use Illuminate\Support\ServiceProvider;

class FjordPagesServiceProvider extends ServiceProvider
{
    /**
     * Commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        Commands\FjordPagesCommand::class,
        Commands\FjordPagesControllerCommand::class,
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

        $this->registerFjordPagesRoutes();

        $this->app->register(FjordPagesRouteServiceProvider::class);
    }

    /**
     * Register fjord pages routes.
     *
     * @return void
     */
    protected function registerFjordPagesRoutes()
    {
        $this->app->singleton('fjord.pages.routes', function ($app) {
            return new FjordPagesRoutes($app);
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

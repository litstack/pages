<?php

namespace Litstack\Pages;

use Closure;
use Ignite\Config\ConfigHandler;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class PagesRoutes
{
    /**
     * Extender.
     *
     * @var array
     */
    protected $extender = [];

    /**
     * Laravel application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * Create new Litstack\PagesRoutes instance.
     *
     * @param  Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Add a closure that extends pages routes.
     *
     * @param  Closure $closure
     * @return void
     */
    public function extend(Closure $closure)
    {
        $this->extender[] = $closure;
    }

    /**
     * Make pages route.
     *
     * @param  ConfigHandler $config
     * @param  string|null   $locale
     * @return void
     */
    public function make(ConfigHandler $config, $locale = null)
    {
        $name = $this->routeName("pages.{$config->collection}", $locale);
        $prefix = $this->routePrefix($config->appRoutePrefix($locale), $locale);

        if ($config->collection == 'root') {
            $prefix = $locale && $locale != (config('translatable.fallback_locale') ?: config('app.locale')) ? "/{$locale}" : '';
        }

        $this->app->booted(function ($app) use ($config, $prefix, $name) {
            $this->resolvePagesRoute($config, $prefix, $name);
        });
    }

    /**
     * Resolve pages route.
     *
     * @param  ConfigHandler $config
     * @param  string        $prefix
     * @param  string        $name
     * @return void
     */
    protected function resolvePagesRoute(ConfigHandler $config, $prefix, $name)
    {
        $route = Route::prefix($prefix)
            ->middleware('web')
            ->get('/{slug}', $config->appController)
            ->config($config->getKey())
            ->name($name);

        foreach ($this->extender as $extender) {
            $extender($route);
        }
    }

    /**
     * Get route prefix.
     *
     * @param  string      $prefix
     * @param  string|null $locale
     * @return string
     */
    protected function routePrefix(string $prefix, string $locale = null)
    {
        if (! $locale) {
            return $prefix;
        }

        return "{$locale}/{$prefix}";
    }

    /**
     * Get route name.
     *
     * @param  string      $name
     * @param  string|null $locale
     * @return string
     */
    protected function routeName(string $name, $locale = null)
    {
        if (! $locale) {
            return $name;
        }

        return "{$locale}.{$name}";
    }
}

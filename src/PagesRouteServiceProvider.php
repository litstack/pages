<?php

namespace Litstack\Pages;

use Ignite\Config\ConfigHandler;
use Ignite\Support\Facades\Config;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;

class PagesRouteServiceProvider extends RouteServiceProvider
{
    /**
     * Map litstack pages routes.
     *
     * @return void
     */
    public function map()
    {
        $this->mapPagesRoutes();
    }

    /**
     * Map pages routes for collection.
     *
     * @param  ConfigHandler $config
     * @return void
     */
    protected function mapPagesRoutesForCollection(ConfigHandler $config)
    {
        if ($config->translatable) {
            return $this->makeTranslatableRoutes($config);
        }

        $this->makePagesRoute($config);
    }

    /**
     * Make translatable routes for locales.
     *
     * @param  ConfigHandler $config
     * @return void
     */
    protected function makeTranslatableRoutes(ConfigHandler $config)
    {
        $locales = config('app.locales') ?: config('translatable.locales');

        foreach ($locales as $locale) {
            $this->makePagesRoute($config, $locale);
        }
    }

    /**
     * Make pages route.
     *
     * @param  ConfigHandler $config
     * @param  string|null   $locale
     * @return void
     */
    protected function makePagesRoute(ConfigHandler $config, $locale = null)
    {
        $this->app['lit.pages.routes']->make($config, $locale);
    }

    /**
     * Map pages routes.
     *
     * @return void
     */
    protected function mapPagesRoutes()
    {
        if (! lit()->installed()) {
            return;
        }

        $files = File::allFiles(lit_config_path());

        foreach ($files as $file) {
            if (! $this->isValidPagesConfig($file)) {
                continue;
            }

            $config = Config::getFromPath($file);

            if (! $config) {
                continue;
            }

            $this->mapPagesRoutesForCollection($config);
        }
    }

    /**
     * Determine if file is a valid pages config.
     *
     * @param  SplFileInfo $file
     * @return bool
     */
    protected function isValidPagesConfig(SplFileInfo $file)
    {
        if ($file->isDir()) {
            return false;
        }

        if (! Str::contains($file, '.php')) {
            return false;
        }

        $namespace = str_replace('/', '\\', 'Lit'.explode('lit/app', str_replace('.php', '', $file))[1]);
        $reflection = new ReflectionClass($namespace);

        if (! $reflection->getParentClass()) {
            return false;
        }

        if (! new $namespace instanceof PagesConfig) {
            return false;
        }

        return true;
    }
}

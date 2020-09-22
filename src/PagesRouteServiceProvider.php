<?php

namespace Litstack\Pages;

use Fjord\Config\ConfigHandler;
use Fjord\Support\Facades\Config;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;

class Litstack\PagesRouteServiceProvider extends RouteServiceProvider
{
    /**
     * Map fjord pages routes.
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
        foreach (config('translatable.locales') as $locale) {
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
        $this->app['fjord.pages.routes']->make($config, $locale);
    }

    /**
     * Map pages routes.
     *
     * @return void
     */
    protected function mapPagesRoutes()
    {
        if (! fjord()->installed()) {
            return;
        }

        $files = File::allFiles(fjord_config_path());

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

        $namespace = str_replace('/', '\\', 'FjordApp'.explode('fjord/app', str_replace('.php', '', $file))[1]);
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

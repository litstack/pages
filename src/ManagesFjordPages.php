<?php

namespace FjordPages;

use FjordPages\Models\FjordPage;
use Illuminate\Support\Str;

trait ManagesFjordPages
{
    /**
     * Get fjord page.
     *
     * @param  string    $slug
     * @return FjordPage
     */
    protected function getFjordPage(string $slug): FjordPage
    {
        $model = FjordPage::class;

        if (! $config = app('request')->route()->getConfig()) {
            abort(404);
        }

        $query = $config->model::where('config_type', get_class($config->getConfig()));

        if ($this->isCurrentRouteTranslatable()) {
            return $query->whereTranslation('t_slug', $slug)
                ->whereTranslation('locale', $this->getCurrentRouteLocale())
                ->firstOrFail();
        }

        return $query->where('slug', $slug)->firstOrFail();
    }

    /**
     * Is current route translatable.
     *
     * @return bool
     */
    protected function isCurrentRouteTranslatable()
    {
        return Str::startsWith(
            app('request')->route()->getName(),
            config('translatable.locales')
        );
    }

    /**
     * Get current route locale.
     *
     * @return string|null
     */
    protected function getCurrentRouteLocale()
    {
        if (! $this->isCurrentRouteTranslatable()) {
            return;
        }

        return explode('.', app('request')->route()->getName())[0];
    }
}

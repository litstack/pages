<?php

namespace FjordPages;

use Illuminate\Support\Str;
use FjordPages\Models\FjordPage;

trait ManagesFjordPages
{
    /**
     * Get fjord page.
     *
     * @param string $slug
     * @return FjordPage
     */
    protected function getFjordPage(string $slug): FjordPage
    {
        if ($this->isCurrentRouteTranslatable()) {
            return FjordPage::whereTranslation('t_slug', $slug)
                ->whereTranslation('locale', $this->getCurrentRouteLocale())
                ->firstOrFail();
        }

        return FjordPage::where('slug', $slug)->firstOrFail();
    }

    /**
     * Is current route translatable.
     *
     * @return boolean
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
        if (!$this->isCurrentRouteTranslatable()) {
            return;
        }

        return explode('.', app('request')->route()->getName())[0];
    }
}

<?php

namespace FjordPages;

use Fjord\Crud\Fields\Route\RouteCollection;
use Illuminate\Database\Eloquent\Collection;

class FjordPagesCollection extends Collection
{
    /**
     * Register pages to route collection.
     *
     * @param  string          $name
     * @param  RouteCollection $collection
     * @return void
     */
    public function registerToRouteCollection(string $name, RouteCollection $collection)
    {
        $collection->group($name, function ($group) {
            $this->map(fn ($page) => $group->route($page->title, fn () => $page->route));
        });
    }
}

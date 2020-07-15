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
    public function addToRouteCollection(string $name, RouteCollection $collection)
    {
        if ($this->isEmpty()) {
            return;
        }

        $collection->group($name, $this->first()->collection, function ($group) {
            $this->map(function ($page) use ($group) {
                $group->route($page->title, $page->id, function ($locale) use ($page) {
                    return $page->getRoute($locale);
                });
            });
        });
    }
}

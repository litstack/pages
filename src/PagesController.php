<?php

namespace Litstack\Pages;

use Ignite\Crud\Controllers\CrudController;
use Illuminate\Database\Eloquent\Builder;
use Litstack\Pages\Models\Page;

class PagesController extends CrudController
{
    /**
     * The Model class.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Fill model on store.
     *
     * @param  Page $model
     * @return void
     */
    public function fillOnStore($model)
    {
        $model->collection = $this->config->collection;
        $model->config_type = get_class($this->config->getConfig());
    }

    /**
     * Initial query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->model::where('collection', $this->config->collection);
    }
}

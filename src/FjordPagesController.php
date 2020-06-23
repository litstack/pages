<?php

namespace FjordPages;

use FjordPages\Models\FjordPage;
use Illuminate\Database\Eloquent\Builder;
use Fjord\Crud\Controllers\CrudController;

class FjordPagesController extends CrudController
{
    /**
     * The Model class.
     *
     * @var string
     */
    protected $model = FjordPage::class;

    /**
     * Fill model on store
     *
     * @param Page $model
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

<?php

namespace FjordPages;

use Fjord\Crud\CrudShow;
use Fjord\Crud\CrudIndex;
use Illuminate\Support\Str;
use FjordPages\Models\FjordPage;
use Fjord\Crud\Config\CrudConfig;
use Fjord\Crud\Fields\Block\Repeatables;

abstract class PagesConfig extends CrudConfig
{
    /**
     * Pages model class.
     *
     * @var string
     */
    public $model = FjordPage::class;

    /**
     * Application route prefix.
     *
     * @return string
     */
    abstract public function appRoutePrefix(string $locale = null);

    /**
     * Make repeatbles that should be available for pages.
     *
     * @param Repeatables $rep
     * @return void
     */
    abstract public function repeatables(Repeatables $rep);

    /**
     * Page route prefix in the fjord backend.
     *
     * @return string
     */
    public function fjordRoutePrefix()
    {
        return "pages/" . Str::slug($this->collection());
    }

    /**
     * Page route prefix in the fjord backend.
     *
     * @return string
     */
    public function routePrefix()
    {
        return $this->fjordRoutePrefix();
    }

    /**
     * Determine if pages are translatable.
     *
     * @return boolean
     */
    public function translatable()
    {
        return config('translatable') ? true : false;
    }

    /**
     * Get pages collection name.
     *
     * @return string
     */
    public function collection()
    {
        return Str::snake(str_replace('Config', '', class_basename(static::class)));
    }

    /**
     * Build index table.
     *
     * @param \Fjord\Crud\CrudIndex $table
     * @return void
     */
    public function index(CrudIndex $container)
    {
        $container->table(fn ($table) => $this->indexTableColumns($table))
            ->search('title');
    }

    /**
     * Build index table columnds.
     *
     * @param \Fjord\Vue\Crud\CrudTable $table
     * @return void
     */
    public function indexTableColumns($table)
    {
        $this->makeTitleColumns($table);

        $table->col('Url ' . fa('external-link-alt'))
            ->value('<a href="{uri}" target="_blank">{uri}</a>')
            ->link(false);
    }

    /**
     * Make title column.
     *
     * @param \Fjord\Vue\Crud\CrudTable $table
     * @return void
     */
    protected function makeTitleColumns($table)
    {
        $table->col('Title')
            ->value('{' . $this->getTitleColumnName() . '}');
    }

    /**
     * Get title column name.
     *
     * @return string
     */
    protected function getTitleColumnName()
    {
        return $this->translatable() ? 't_title' : 'title';
    }

    /**
     * Setup create and edit form.
     *
     * @param \Fjord\Crud\CrudShow $container
     * @return void
     */
    public function show(CrudShow $container)
    {
        $container->card(function ($form) {

            $form->input($this->getTitleColumnName())
                ->translatable($this->translatable())
                ->creationRules('required')
                ->rules('min:2')
                ->title('Title');

            $this->makeContentBlock($form);
        });

        $container->meta();
    }


    /**
     * Make content block.
     *
     * @param CrudShow $form
     * @return void
     */
    protected function makeContentBlock($form)
    {
        $form->block('content')
            ->title('Content')
            ->repeatables(function ($rep) {
                $this->repeatables($rep);
            });
    }
}

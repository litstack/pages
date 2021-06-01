<?php

namespace Litstack\Pages;

use Ignite\Crud\CrudShow;
use Ignite\Crud\CrudIndex;
use Illuminate\Support\Str;
use Litstack\Pages\Models\Page;
use Ignite\Crud\Config\CrudConfig;
use Litstack\Meta\Traits\CrudHasMeta;
use Ignite\Crud\Fields\Block\Repeatables;

abstract class PagesConfig extends CrudConfig
{
    use CrudHasMeta;

    /**
     * Pages model class.
     *
     * @var string
     */
    public $model = Page::class;

    /**
     * Application route prefix.
     *
     * @return string
     */
    abstract public function appRoutePrefix(string $locale = null);

    /**
     * Make repeatbles that should be available for pages.
     *
     * @param  Repeatables $rep
     * @return void
     */
    abstract public function repeatables(Repeatables $rep);

    /**
     * Page route prefix in the listack backend.
     *
     * @return string
     */
    public function litstackRoutePrefix()
    {
        return 'pages/'.Str::slug($this->collection());
    }

    /**
     * Page route prefix in the litstack backend.
     *
     * @return string
     */
    public function routePrefix()
    {
        return $this->litstackRoutePrefix();
    }

    /**
     * Determine if pages are translatable.
     *
     * @return bool
     */
    public function translatable()
    {
        return lit()->isAppTranslatable();
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
     * @param  \Ignite\Crud\CrudIndex $table
     * @return void
     */
    public function index(CrudIndex $container)
    {
        $container->table(fn ($table) => $this->indexTableColumns($table))
            ->search($this->getTitleColumnName());
    }

    /**
     * Build index table columnds.
     *
     * @param  \Ignite\Vue\Crud\CrudTable $table
     * @return void
     */
    public function indexTableColumns($table)
    {
        $this->makeTitleColumns($table);

        $table->col('Url '.fa('external-link-alt'))
            ->value('<a href="{uri}" target="_blank">{uri}</a>')
            ->link(false);
    }

    /**
     * Make title column.
     *
     * @param  \Ignite\Vue\Crud\CrudTable $table
     * @return void
     */
    protected function makeTitleColumns($table)
    {
        $table->col('Title')
            ->value('{'.$this->getTitleColumnName().'}');
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
     * Get title column name.
     *
     * @return string
     */
    protected function getSlugColumnName()
    {
        return $this->translatable() ? 't_slug' : 'slug';
    }

    /**
     * Setup create and edit form.
     *
     * @param  \Ignite\Crud\CrudShow $page
     * @return void
     */
    public function show(CrudShow $page)
    {
        $page->card(function ($form) {
            $form->input($this->getTitleColumnName())
                ->translatable($this->translatable())
                ->creationRules('required')
                ->rules('min:2')
                ->title('Title')
                ->width(8)
                ->hint('Seitentitel in Litstack / Aus dem Titel wird auch der Slug für die URL generiert');
            
            $form->modal('change_slug')
                ->title('Slug')
                ->variant('primary')
                ->preview(url('/')."/<b>{".$this->getSlugColumnName()."}</b>")
                ->name('Change Slug')
                ->form(function ($modal) {
                    $modal->input($this->getSlugColumnName())
                        ->width(12)
                        ->title('Slug');
                })->width(4);

            $form->image('page_image')
                ->title('Page-Image')
                ->hint('Kann z.B. als Vorschaubild oder Header-Bild eingesetzt werden.')
                ->maxFiles(1)
                ->expand()
                ->width(6);

            $form->textarea('page_excerpt')
                ->title('Page-Excerpt')
                ->translatable($this->translatable())
                ->hint('Vorschautext z.B. für Übersichtsseiten')
                ->width(6);

            $this->prependForm($form);
        });

        $page->card(function ($form) {
            $form->input('h1')
                ->translatable($this->translatable())
                ->hint('Kurz, aber aussagekräftig (5 Wörter oder weniger), Thematik der Headline entspricht der Thematik im Content')
                ->title('H1');

            $this->makeContentBlock($form);
        });

        $this->appendForm($page);
    }

    public function prependForm(CrudShow $form)
    {
        //
    }

    public function appendForm(CrudShow $page)
    {
    }

    /**
     * Make content block.
     *
     * @param  CrudShow $form
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

<?php

namespace FjordPages\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Cviebrock\EloquentSluggable\Sluggable;
use Fjord\Config\ConfigHandler;
use Fjord\Crud\Models\FjordFormModel;
use Fjord\Crud\Models\Traits\TrackEdits;
use Fjord\Crud\Models\Traits\Translatable;
use Fjord\Support\Facades\Config;
use FjordPages\FjordPagesCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Spatie\MediaLibrary\HasMedia as HasMediaContract;

/**
 * @method static void collection(string $collection)
 */
class FjordPage extends FjordFormModel implements TranslatableContract, HasMediaContract
{
    use Sluggable, TrackEdits, Translatable;

    /**
     * Translation model name.
     *
     * @var string
     */
    public $translationModel = FjordPageTranslation::class;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = ['title', 'value'];

    /**
     * Translated attributes.
     *
     * @var array
     */
    public $translatedAttributes = ['t_title', 't_slug'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['uri', 'translation'];

    /**
     * Eager loads.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Unique by title + locale.
     *
     * @param  Builder $query
     * @param  mixed   $model
     * @param  mixed   $attribute
     * @param  array   $config
     * @param  string  $slug
     * @return Builder
     */
    public function scopeWithUniqueSlugConstraints(Builder $query, $model, $attribute, $config, $slug)
    {
        return $query->where('config_type', $model->config_type);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    /**
     * Collection query.
     *
     * @param  Builder $query
     * @param  string  $collection
     * @return void
     */
    public function scopeCollection($query, $collection)
    {
        $query->where('collection', $collection);
    }

    /**
     * Create a new FjordPagesCollection instance.
     *
     * @param  array                $models
     * @return FjordPagesCollection
     */
    public function newCollection(array $models = [])
    {
        return new FjordPagesCollection($models);
    }

    /**
     * Content repeatables.
     *
     * @return Relation
     */
    public function content()
    {
        return $this->repeatables('content');
    }

    /**
     * Get route.
     *
     * @param  string $locale
     * @return Route
     */
    public function getRoute(string $locale = null)
    {
        if (! $this->id) {
            return;
        }

        if (! $this->slug) {
            return;
        }

        // dd(collect(RouteFacade::getRoutes()->getRoutes())->map(function ($route) {
        //     return $route->uri.' => '.$route->getName();
        // }));

        return route($this->getRouteName($locale), [
            'slug' => $this->slug,
        ], false);
    }

    /**
     * Get route name.
     *
     * @param  string|null $locale
     * @return string
     */
    public function getRouteName(string $locale = null)
    {
        if ($this->isTranslatable() && ! $locale) {
            $locale = app()->getLocale();
        }

        if ($this->isTranslatable()) {
            return "{$locale}.pages.{$this->config->collection}";
        }

        return "pages.{$this->config->collection}";
    }

    /**
     * Get config handler.
     *
     * @return ConfigHandler
     */
    public function getConfigAttribute()
    {
        if (! $this->config_type) {
            return;
        }

        return Config::get($this->config_type);
    }

    /**
     * Get uri attribute.
     *
     * @return string
     */
    public function getUriAttribute()
    {
        return $this->getRoute();
    }

    /**
     * Determine if page is translatable.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->config->translatable ?? false;
    }

    /**
     * Get page attribute.
     *
     * @param  string $key
     * @return mixed
     */
    protected function getPageAttribute($key)
    {
        if ($this->isTranslatable()) {
            return $this->getAttribute("t_{$key}");
        }

        return $this->getAttribute($key);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'slug') {
            return $this->getPageAttribute($key);
        }

        if ($key === 'title') {
            return $this->getPageAttribute($key);
        }

        return $this->getAttribute($key);
    }
}

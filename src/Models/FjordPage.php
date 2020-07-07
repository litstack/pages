<?php

namespace FjordPages\Models;

use Illuminate\Routing\Route;
use Fjord\Config\ConfigHandler;
use Fjord\Support\Facades\Config;
use FjordPages\FjordPagesCollection;
use Fjord\Crud\Models\Traits\HasMedia;
use Fjord\Crud\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Fjord\Crud\Models\Traits\TrackEdits;
use Fjord\Crud\Models\Traits\Translatable;
use FjordPages\Models\FjordPageTranslation;
use Fjord\Crud\Fields\Route\RouteCollection;
use Spatie\MediaLibrary\HasMedia as HasMediaContract;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

/**
 * @method static void collection(string $collection)
 */
class FjordPage extends Model implements TranslatableContract, HasMediaContract
{
    use TrackEdits, Translatable, Sluggable, HasMedia;

    /**
     * Translation model name.
     *
     * @var string
     */
    public $translationModel = FjordPageTranslation::class;

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = ['title'];

    /**
     * Translated attributes
     *
     * @var array
     */
    public $translatedAttributes = ['t_title', 't_slug'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['uri'];

    /**
     * Eager loads.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Collection query.
     *
     * @param Builder $query
     * @param string $collection
     * @return void
     */
    public function scopeCollection($query, $collection)
    {
        $query->where('collection', $collection);
    }

    /**
     * Create a new FjordPagesCollection instance.
     *
     * @param  array  $models
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
     * @param string $locale
     * @return Route
     */
    public function getRoute(string $locale = null)
    {
        if (!$this->id) {
            return;
        }

        if (!$this->slug) {
            return;
        }

        return route($this->getRouteName($locale), [
            'slug' => $this->slug
        ], false);
    }

    /**
     * Get route name.
     *
     * @param string|null $locale
     * @return string
     */
    public function getRouteName(string $locale = null)
    {
        if ($this->isTranslatable() && !$locale) {
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
        if (!$this->config_type) {
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
     * @return boolean
     */
    public function isTranslatable()
    {
        return $this->config->translatable ?? false;
    }

    /**
     * Get page attribute.
     *
     * @param string $key
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
     * @param  string  $key
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

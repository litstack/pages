<?php

namespace Litstack\Pages\Models;

use Illuminate\Support\Str;
use Litstack\Meta\Metaable;
use Illuminate\Routing\Route;
use Ignite\Config\ConfigHandler;
use Litstack\Meta\Traits\HasMeta;
use Ignite\Support\Facades\Config;
use Litstack\Pages\PagesCollection;
use Ignite\Crud\Models\LitFormModel;
use Ignite\Crud\Models\Traits\Sluggable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia as HasMediaContract;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

/**
 * @method static void collection(string $collection)
 */
class Page extends LitFormModel implements TranslatableContract, HasMediaContract, Metaable
{
    use Sluggable, Translatable, HasMeta;

    /**
     * Database table name.
     *
     * @var string
     */
    public $table = 'lit_pages';

    /**
     * Translation foreign key.
     *
     * @var string
     */
    protected $translationForeignKey = 'lit_page_id';

    /**
     * Translation model name.
     *
     * @var string
     */
    public $translationModel = PageTranslation::class;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'value', 'collection', 'config_type',
    ];

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
     * Get translations array.
     *
     * @return array
     */
    public function getTranslationsArray(): array
    {
        return parent::getTranslationsArray();
    }

    /**
     * Get current litstack page.
     *
     * @return self|null
     */
    public static function current()
    {
        if (! $route = request()->route()) {
            return;
        }

        $name = $route->getName();

        if (Str::startsWith($name, $locale = app()->getLocale() . '.')) {
            $name = Str::replaceFirst($locale, '', $name);
        }

        if (! $config = lit()->config($name)) {
            return;
        }

        $slug = request()->route()->parameter('slug');

        $query = static::collection($config->collection);

        if (! $config->translatable) {
            $query->where('slug', $slug);
        } else {
            $query->whereHas('translation', function ($query) use ($slug) {
                $query->where('locale', app()->getLocale())->where('t_slug', $slug);
            });
        }

        return $query->first();
    }

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
     * Create a new PagesCollection instance.
     *
     * @param  array           $models
     * @return PagesCollection
     */
    public function newCollection(array $models = [])
    {
        return new PagesCollection($models);
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
     * content repeatables.
     *
     * @return Relation
     */
    public function content2()
    {
        return $this->repeatables('content2');
    }

    /**
     * content repeatables.
     *
     * @return Relation
     */
    public function content3()
    {
        return $this->repeatables('content3');
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

        if (! $slug = $this->getPageAttribute('slug', $locale)) {
            return;
        }

        return route($this->getRouteName($locale), [
            'slug' => $slug,
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
            return "{$locale}.pages.{$this->collection}";
        }

        return "pages.{$this->collection}";
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
     * @param string $locale
     * @return mixed
     */
    protected function getPageAttribute($key, $locale = null)
    {
        if ($this->isTranslatable()) {
            if ($translation = $this->translate($locale ?? app()->getLocale())) {
                return $translation->{"t_{$key}"};
            }

            return;
        }
        return $this->attributes[$key] ?? null;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if ($key === 'slug') {
            return $this->getPageAttribute($key, app()->getLocale());
        }

        if ($key === 'title') {
            return $this->getPageAttribute($key, app()->getLocale());
        }

        return parent::getAttribute($key);
    }
}

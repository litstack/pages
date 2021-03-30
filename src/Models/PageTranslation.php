<?php

namespace Litstack\Pages\Models;

use Ignite\Crud\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    use Sluggable;

    /**
     * Database table name.
     *
     * @var string
     */
    public $table = 'lit_page_translations';

    /**
     * Timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = ['t_title', 't_slug', 'value'];

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = ['value' => 'json'];

    /**
     * Appended accessort.
     *
     * @var array
     */
    protected $appends = ['title'];

    /**
     * [title] attribute.
     *
     * @return void
     */
    public function getTitleAttribute()
    {
        return $this->t_title;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            't_slug' => [
                'source' => 't_title',
            ],
        ];
    }

    /**
     * Parent page.
     *
     * @return void
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'lit_page_id');
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
        return $query->where('locale', $model->locale)->whereHas('page', function ($pageQuery) use ($model) {
            $pageQuery->where('config_type', $model->page->config_type);
        });
    }
}

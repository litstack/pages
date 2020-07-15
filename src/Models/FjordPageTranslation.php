<?php

namespace FjordPages\Models;

use Fjord\Crud\Models\Traits\Sluggable;
use Fjord\Crud\Models\Traits\TrackEdits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FjordPageTranslation extends Model
{
    use Sluggable, TrackEdits;

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
    protected $fillable = ['t_title', 'value'];

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            't_slug' => [
                'source' => 't_title',
            ],
        ];
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
        return $query->where('locale', $model->locale);
    }
}

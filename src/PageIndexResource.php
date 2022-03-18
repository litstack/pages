<?php

namespace Litstack\Pages;

use Ignite\Crud\CrudResource;

class PageIndexResource extends CrudResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $attributes = [
            'uri' => $this->uri,
            'id'  => $this->id,
        ];

        if ($this->config->translatable) {
            $attributes['t_title'] = $this->t_title;
        } else {
            $attributes['title'] = $this->title;
        }

        return $attributes;
    }
}

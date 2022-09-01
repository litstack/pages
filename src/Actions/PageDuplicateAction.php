<?php

namespace Litstack\Pages\Actions;

use Ignite\Crud\Models\Repeatable;
use Ignite\Crud\Models\Translations\RepeatableTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Litstack\Meta\Models\Meta;
use Litstack\Meta\Models\Translations\MetaTranslation;
use Litstack\Pages\Models\Page;
use Litstack\Pages\Models\PageTranslation;

class PageDuplicateAction
{
    /**
     * Run the action.
     *
     * @param  Collection  $models
     * @return JsonResponse
     */
    public function run(Collection $models)
    {
        // 
        foreach($models as $model) {
            $this->duplicatePage($model);
        }

        return response()->success('Action executed.');
    }

    protected function duplicatePage(Page $page)
    {
        $new = $page->replicate();
        $new->push();

        $this->duplicateRelations($new, 'lit_page_id', PageTranslation::where('lit_page_id', $page->id)->get());

        $repeatables = Repeatable::where('model_type', Page::class)->where('model_id', $page->id)->get();
        foreach($repeatables as $repeatable) {
            $newRelation = $repeatable->replicate();
            $newRelation->model_id = $new->id;
            $newRelation->push();
            $t = RepeatableTranslation::where('lit_repeatable_id', $repeatable->id)->get();
            $this->duplicateRelations($newRelation, 'lit_repeatable_id', $t);
        }

        $meta = Meta::where('model_type', Page::class)->where('model_id', $page->id)->first();
        $newRelation = $meta->replicate();
        $newRelation->model_id = $new->id;
        $newRelation->push();
        $t = MetaTranslation::where('meta_id', $meta->id)->get();
        $this->duplicateRelations($newRelation, 'meta_id', $t);
        Meta::where('model_type', Page::class)->where('model_id', $new->id)->take(1)->delete();
    }

    protected function duplicateRelations($page, $attr, Collection $relations)
    {
        foreach($relations as $relation) {
            $newRelation = $relation->replicate();
            $newRelation->$attr = $page->id;
            $newRelation->push();
        }
    }
}

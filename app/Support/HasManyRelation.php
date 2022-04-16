<?php

namespace App\Support;

trait HasManyRelation {

    public function storeHasMany($relations)
    {
        $this->save();

        foreach($relations as $relation => $items) {
            $this->insertRelation($this, $relation, $items);
        }
    }

    protected function insertRelation($context, $relation, $items)
    {
        if(strpos($relation, '.') !== false) {
            list($key, $sub) = explode('.', $relation);
            foreach($items as $item) {
                $model = $context->{$key}()->getModel();
                $temp = $model->fill($item);
                $results = $context->{$key}()->save($temp);
                if(isset($item[$sub])) {
                    $this->insertRelation($results, $sub, $item[$sub]);
                }
            }
        } else {
            $list = [];
            foreach($items as $item) {
                $model = $context->{$relation}()->getModel();
                $list[] = $model->fill($item);
            }
            $context->{$relation}()->saveMany($list);
        }
    }

    public function updateHasMany($relations)
    {
        $this->save();

        foreach($relations as $relation => $items) {

            $this->updateRelation($this, $relation, $items);
        }
    }

    protected function updateRelation($context, $relation, $items)
    {
        if(strpos($relation, '.') !== false) {
            list($key, $sub) = explode('.', $relation);

            $updatedIds = [];
            $newIds = [];
            $newItems = [];
            foreach($items as $item) {
                $model = $context->{$key}()->getModel();
                $localKey = $model->getKeyName();
                $foreginKey = $context->{$key}()->getForeignKeyName();
                $parentId = $context->getAttribute($context->getKeyName());
                if(isset($item[$foreginKey])) {
                    // update
                    $localId = $item[$localKey];
                    $found = $model->where($foreginKey, $parentId)
                        ->where($localKey, $localId)
                        ->first();

                    if($found) {
                        $found->fill($item);
                        $found->save();

                        $updatedIds[] = $localId;

                        if(isset($item[$sub])) {
                            $this->updateRelation($found, $sub, $item[$sub]);
                        }
                    }
                } else {
                    $temp = $model->fill($item);
                    $result = $context->{$key}()->save($temp);
                    $newIds[] = $result->id;
                    $this->insertRelation($result, $sub, $item[$sub]);
                }
            }

            $model = $context->{$key}()->getModel();
            $localKey = $model->getKeyName();
            $foreginKey = $context->{$key}()->getForeignKeyName();
            $parentId = $context->getAttribute($context->getKeyName());
            $x = $model->whereNotIn($localKey, array_merge($updatedIds, $newIds))
                ->where($foreginKey, $parentId)
                ->get();

            foreach($x as $y) {
                $y->{$sub}()->delete();
                $y->delete();
            }
        } else {
            $updatedIds = [];
            $newItems = [];

            // 1. filter and update
            foreach($items as $item) {
                $model = $context->{$relation}()->getModel();
                $localKey = $model->getKeyName();
                $foreginKey = $context->{$relation}()->getForeignKeyName();
                $parentId = $context->getAttribute($context->getKeyName());

                if(isset($item[$foreginKey])) {
                    // update
                    $localId = $item[$localKey];
                    $found = $model->where($foreginKey, $parentId)
                        ->where($localKey, $localId)
                        ->first();

                    if($found) {
                        $found->fill($item);
                        $found->save();

                        $updatedIds[] = $localId;
                    }
                } else {
                    // new
                    $newItems[] = $model->fill($item);
                }
            }

            // 2. delete not-updated items
            $model = $context->{$relation}()->getModel();
            $localKey = $model->getKeyName();
            $foreginKey = $context->{$relation}()->getForeignKeyName();
            $parentId = $context->getAttribute($context->getKeyName());
            $model->whereNotIn($localKey, $updatedIds)
                ->where($foreginKey, $parentId)
                ->delete();

            if(count($newItems)) {
                // 3. save new items
                $context->{$relation}()->saveMany($newItems);
            }
        }
    }
}

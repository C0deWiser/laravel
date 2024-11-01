<?php

namespace Codewiser;

use Illuminate\Container\Attributes\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

if (! function_exists('Codewiser\tag')) {
    function tag($tag): string
    {
        if ($tag instanceof Model) {
            $model = class_basename($tag->getMorphClass());
            if ($tag->incrementing) {
                return "$model:{$tag->getKey()}";
            } else {
                if ($tag instanceof Pivot) {
                    return "$model:{$tag->getForeignKey()},{$tag->getRelatedKey()}";
                }
                return $model;
            }
        }

        $tag = match (true) {
            $tag instanceof \BackedEnum => class_basename($tag).':'.$tag->value,
            $tag instanceof \UnitEnum => class_basename($tag).':'.$tag->name,

            default => $tag,
        };

        if ($tag instanceof \DateTimeInterface) {
            $tag = $tag->format('c');
        }

        return $tag;
    }
}

if (! function_exists('Codewiser\ability')) {
    function ability($ability)
    {
        if (is_array($ability) && is_callable($ability, true)) {
            $ability = $ability[1];
        }
        return $ability;
    }
}

if (! function_exists('Codewiser\relation')) {
    function relation($relation)
    {
        if (is_array($relation) && is_callable($relation, true)) {
            $relation = $relation[1];
        }
        return $relation;
    }
}
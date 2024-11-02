<?php

namespace Codewiser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

if (!function_exists('Codewiser\tag')) {
    /**
     * Make tag for a job.
     */
    function tag($tag): string
    {
        if ($tag instanceof Model) {
            $model = class_basename($tag->getMorphClass());
            if ($tag->incrementing) {
                $key = $tag->getKey() ?? 'null';
            } elseif ($tag instanceof Pivot) {
                $key = implode(',', [$tag->getForeignKey() ?? 'null', $tag->getRelatedKey() ?? 'null']);
            } else {
                $key = null;
            }
            return $model.($key ? "#$key" : '');
        }

        $tag = match (true) {
            $tag instanceof \BackedEnum => class_basename($tag).'::'.$tag->value,
            $tag instanceof \UnitEnum   => class_basename($tag).'::'.$tag->name,
            default                     => $tag,
        };

        if ($tag instanceof \DateTimeInterface) {
            $tag = $tag->format('c');
        }

        return $tag;
    }
}

if (!function_exists('Codewiser\ability')) {
    /**
     * Resolve ability name from a method reference.
     */
    function ability(callable $ability): string
    {
        if (is_array($ability) && is_callable($ability, true)) {
            $ability = $ability[1];
        }
        return $ability;
    }
}

if (!function_exists('Codewiser\relation')) {
    /**
     * Resolve relation name from a method reference.
     */
    function relation(callable $relation): string
    {
        if (is_array($relation) && is_callable($relation, true)) {
            $relation = $relation[1];
        }
        return $relation;
    }
}
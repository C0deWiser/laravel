<?php

namespace Codewiser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

if (!function_exists('Codewiser\tag')) {
    /**
     * Make tag for a job.
     *
     * @deprecated
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
     *
     * @param callable $ability
     *
     * @deprecated
     */
    function ability($ability): string
    {
        return ref($ability);
    }
}

if (!function_exists('Codewiser\relation')) {
    /**
     * Resolve relation name from a method reference.
     *
     * @param callable $relation
     *
     * @deprecated
     */
    function relation($relation): string
    {
        return ref($relation);
    }
}

if (!function_exists('Codewiser\ref')) {
    /**
     * Resolve method name from a method reference.
     *
     * @param callable $callable
     *
     * @deprecated
     */
    function ref($callable): string
    {
        if (is_array($callable) && is_callable($callable, true)) {
            $callable = $callable[1];
        }
        return $callable;
    }
}
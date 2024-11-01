<?php

namespace Codewiser\Database\Eloquent\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Allows to make queries to a pivot table.
 *
 * @mixin Builder
 */
trait HasPivot
{
    /**
     * Add a relationship count / exists condition to the pivot relation with where clauses.
     */
    public function whereHasMany(
        BelongsToMany|string $relation,
        ?Closure $callback = null,
        string $operator = '>=',
        int $count = 1,
        string $boolean = 'and'
    ): static {

        $callback = function (Builder $builder) use ($callback, $relation) {
            // As we modify given relation, will use its clone
            $relation = is_string($relation) ? $this->getRelationWithoutConstraints($relation) : clone $relation;

            if ($relation instanceof BelongsToMany) {
                call_user_func($callback, $relation->setQuery($builder->getQuery()));

                return $builder;
            }

            throw new \InvalidArgumentException('Only BelongsToMany relations are applicable.');
        };

        return $this->has($relation, $operator, $count, $boolean, $callback);
    }

    /**
     * Add a relationship count / exists condition to the pivot relation with where clauses and an "or".
     */
    public function orWhereHasMany(
        BelongsToMany|string $relation,
        ?Closure $callback = null
    ): static {
        return $this->whereHasMany($relation, $callback, '>=', 1, 'or');
    }

    /**
     * Add a relationship count / exists condition to the pivot relation with where clauses.
     */
    public function whereDoesntHaveMany(BelongsToMany|string $relation, ?Closure $callback = null): static
    {
        return $this->whereHasMany($relation, $callback, '<');
    }

    /**
     * Add a relationship count / exists condition to the pivot relation with where clauses and an "or".
     */
    public function orWhereDoesntHaveMany(BelongsToMany|string $relation, ?Closure $callback = null): static
    {
        return $this->whereHasMany($relation, $callback, '<', 1, 'or');
    }

    /**
     * Add a "BelongsToMany" relationship where clause to the query.
     */
    public function whereBelongsToMany(Model|Collection $related, BelongsToMany|string $relationshipName = null, string $boolean = 'and'): static
    {
        if (!$related instanceof Collection) {
            $relatedCollection = $related->newCollection([$related]);
        } else {
            $relatedCollection = $related;

            $related = $relatedCollection->first();
        }

        if ($relatedCollection->isEmpty()) {
            throw new \InvalidArgumentException('Collection given to whereBelongsToMany method may not be empty.');
        }

        if ($relationshipName === null) {
            $relationshipName = str(class_basename($related))->plural()->camel()->toString();
        }

        if (is_string($relationshipName)) {
            try {
                $relationship = $this->model->{$relationshipName}();

                if (!$relationship instanceof BelongsToMany) {
                    throw RelationNotFoundException::make($this->model, $relationshipName, BelongsToMany::class);
                }

            } catch (\BadMethodCallException) {
                throw RelationNotFoundException::make($this->model, $relationshipName);
            }
        }

        $in = fn(Builder $builder) => $builder->whereKey($relatedCollection->modelKeys());

        if ($boolean == 'and') {
            return $this->whereHas($relationshipName, $in);
        } else {
            return $this->orWhereHas($relationshipName, $in);
        }
    }

    /**
     * Add an "BelongsToMany" relationship with an "or where" clause to the query.
     */
    public function orWhereBelongsToMany(Model|Collection $related, BelongsToMany|string $relationshipName = null): static
    {
        return $this->whereBelongsToMany($related, $relationshipName, 'or');
    }
}

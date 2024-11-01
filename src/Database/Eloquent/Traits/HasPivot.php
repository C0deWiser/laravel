<?php

namespace Codewiser\Database\Eloquent\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Allows to make queries to a pivot table.
 *
 * @mixin Builder
 */
trait HasPivot
{
    /**
     * Add a relationship count / exists condition to the pivot relation with where clauses and an "or".
     */
    public function orWhereBelongsToMany(
        BelongsToMany|string $relation,
        Closure $callback,
        string $operator = '>=',
        int $count = 1
    ): static {
        return $this->whereBelongsToMany($relation, $callback, $operator, $count, 'or');
    }

    /**
     * Add a relationship count / exists condition to the pivot relation with where clauses.
     */
    public function whereBelongsToMany(
        BelongsToMany|string $relation,
        Closure $callback,
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
}

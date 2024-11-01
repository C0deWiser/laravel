<?php

namespace Codewiser\Database\Eloquent\Traits;

use Illuminate\Contracts\Database\Query\Builder;

/**
 * @mixin Builder
 */
trait HasDeletedAt
{
    /**
     * @return static
     */
    public function onlyTrashed($boolean = 'and')
    {
        return $this->where($this->qualifyColumn('deleted_at'), '<=', now(), boolean: $boolean);
    }

    /**
     * @return static
     */
    public function orOnlyTrashed()
    {
        return $this->onlyTrashed('or');
    }

    /**
     * @return static
     */
    public function withoutTrashed($boolean = 'and')
    {
        return $this->where(fn(Builder $builder) => $builder
            ->whereNull($this->qualifyColumn('deleted_at'))
            ->orWhere($this->qualifyColumn('deleted_at'), '>', now()),
            boolean: $boolean
        );
    }

    /**
     * @return static
     */
    public function orWithoutTrashed()
    {
        return $this->withoutTrashed('or');
    }
}

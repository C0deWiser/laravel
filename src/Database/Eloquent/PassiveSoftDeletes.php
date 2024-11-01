<?php

namespace Codewiser\Database\Eloquent;


use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Disabled by default SoftDeletes trait.
 */
trait PassiveSoftDeletes
{
    use SoftDeletes;

    public static function bootSoftDeletes(): void
    {
        static::addGlobalScope(new PassiveSoftDeletingScope);
    }

    public function trashed(): bool
    {
        $deleted_at = $this->{$this->getDeletedAtColumn()};

        return ! is_null($deleted_at) && $deleted_at <= now();
    }

    public function alive(): bool
    {
        $deleted_at = $this->{$this->getDeletedAtColumn()};

        return is_null($deleted_at) || $deleted_at > now();
    }
}

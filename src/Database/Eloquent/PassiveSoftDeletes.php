<?php

namespace Codewiser\Database\Eloquent;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Disabled by default SoftDeletes trait.
 *
 * @property null|Carbon $deleted_at
 *
 * @deprecated
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

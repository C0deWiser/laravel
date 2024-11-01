<?php

namespace Codewiser\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PassiveSoftDeletingScope extends SoftDeletingScope
{
    public function apply(Builder $builder, Model $model)
    {
        // Do not apply
    }
}
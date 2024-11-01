<?php

namespace Codewiser\Database\Eloquent\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use JsonSerializable;

class AsStruct implements Castable
{
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments) implements CastsAttributes {
            protected string $structClass;
            protected bool $required = false;

            public function __construct(protected array $arguments)
            {
                foreach ($this->arguments as $argument) {
                    if (is_a($argument, Pivot::class, true)) {
                        $this->structClass = $argument;
                    }
                }

                $this->required = in_array('required', $this->arguments);
            }

            public function get($model, $key, $value, $attributes)
            {
                if (is_null($value) && $this->required) {
                    $value = [];
                }
                if (is_string($value)) {
                    $value = Json::decode($value);
                }

                return is_array($value) ? new $this->structClass($value) : null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return Json::encode($value);
            }
        };
    }

    /**
     * Specify the class for the cast.
     *
     * @param  class-string<Pivot>  $struct
     * @param  bool  $required
     *
     * @return string
     */
    public static function using(string $struct, bool $required = false): string
    {
        $args = [
            $struct,
            $required ? 'required' : 'nullable',
        ];

        return static::class.':'.implode(',', $args);
    }
}

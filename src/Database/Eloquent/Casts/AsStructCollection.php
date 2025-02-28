<?php

namespace Codewiser\Database\Eloquent\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;

/**
 * @deprecated
 */
class AsStructCollection implements Castable
{
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments) implements CastsAttributes {
            protected string $collectionClass;
            protected string $structClass;
            protected bool $required = false;

            public function __construct(protected array $arguments)
            {
                $this->collectionClass = Collection::class;

                foreach ($this->arguments as $argument) {
                    if (is_a($argument, Collection::class, true)) {
                        $this->collectionClass = $argument;
                    }
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

                if (is_array($value)) {
                    $value = array_map(fn($item) => new $this->structClass($item), $value);
                }

                return is_array($value) ? new $this->collectionClass($value) : null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return Json::encode($value);
            }
        };
    }

    /**
     * Specify the collection and/or model for the cast.
     *
     * @param  class-string<Collection|Pivot>  $class
     * @param  null|class-string<Pivot>  $struct
     * @param  bool  $required
     *
     * @return string
     */
    public static function using(string $class, string $struct = null, bool $required = false): string
    {
        $args = [
            $class,
            $struct,
            $required ? 'required' : 'nullable',
        ];

        return static::class.':'.implode(',', array_filter($args));
    }

    /**
     * Specify the collection and/or model for the cast.
     *
     * @param  class-string<Collection|Pivot>  $class
     * @param  null|class-string<Pivot>  $struct
     * @param  bool  $required
     *
     * @return string
     */
    public static function of(string $class, string $struct = null, bool $required = false): string
    {
        return static::using($class, $struct, $required);
    }
}

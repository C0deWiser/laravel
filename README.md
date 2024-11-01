# Laravel Swiss-knife

Packages I might include to every Laravel application

## Structures

Structure is an `array` or `json` attributes with structured interface.

For example:

```php
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Support\Stringable;

/**
 * @property null|Stringable $first_name
 * @property null|Stringable $second_name
 * @property null|Stringable $family_name
 */
class Username extends Pivot
{
    protected function casts(): array
    {
        return [
            'first_name' => AsStringable::class,
            'second_name' => AsStringable::class,
            'family_name' => AsStringable::class,
        ];   
    }
} 
```

Apply `Username` struct to `User` model:

```php
use Illuminate\Database\Eloquent\Model;
use Codewiser\Database\Eloquent\Casts\AsStruct;

/**
 * @property null|Username $name
 */
class User extends Model
{
    protected function casts(): array
    {
        return [
            'name' => AsStruct::using(Username::class)
        ];
    }    
}
```

You can make it not-nullable:

```php
use Illuminate\Database\Eloquent\Model;
use Codewiser\Database\Eloquent\Casts\AsStruct;

/**
 * @property Username $name
 */
class User extends Model
{
    protected function casts(): array
    {
        return [
            'name' => AsStruct::using(Username::class, required: true)
        ];
    }    
}
```

## Structure collections

The same way, you may cast collections of custom structs:

```php
use Codewiser\Database\Eloquent\Casts\AsStructCollection;
use \Illuminate\Support\Collection;

/**
 * @property null|Collection<Contact> $contacts_1
 * @property null|ContactCollection<Contact> $contacts_2
 * @property Collection<Contact> $contacts_3
 */
class User extends Model
{
    protected function casts(): array
    {
        return [
            'contacts_1' => AsStructCollection::using(Contact::class),
            'contacts_2' => AsStructCollection::using(ContactCollection::class, Contact::class),
            'contacts_3' => AsStructCollection::using(Contact::class, required: true),
        ];
    }    
}
```

## HasPivot trait

This trait applicable to any `BelongsToMany` custom `Builder`.

It provides `whereBelongsToMany` and `orWhereBelongsToMany` methods, that 
works alike `whereHas` and `orWhereHas` methods, but sends to a callback not 
a `Builder` instance, but `BelongsToMany` instance.

This allows to make `pivot` calls:

```php
use Illuminate\Database\Eloquent\Builder;
use Codewiser\Database\Eloquent\Traits\HasPivot;

class BookBuilder extends Builder
{
    use HasPivot;
}
```

```php
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

Book::query()->whereBelongsToMany('authors', fn(BelongsToMany $builder) => $builder
    ->wherePivot('role', 'author')
    ->where('name', 'Edgar Allan Poe')
);
```

## Passive SoftDeletes

`PassiveSoftDeletes` traits works alike `SoftDeletes`, but it is disabled by 
default.

Also, it counts record as trashed only then `deleted_at` is reached. So you 
may trash records in perspective.

It comes with `HasDeletedAt` trait, that is applicable to custom builders 
with the same behavior.
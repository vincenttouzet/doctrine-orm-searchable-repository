# doctrine-orm-searchable-repository

This library extends the EntityRepository to add search functionality with a lot of filters.

```php
$filters = [
    'name'        => ['neq' => 'My name'],
    'author.name' => ['in' => ['Foo', 'Bar']],
    'rating'      => ['gte' => 4],
];

$orders = [
    'author.name' => 'ASC',
    'name'        => 'DESC',
];

$entities = $repository->search($filters, $orders);
```


## How to use

Install using composer

```
composer require saf/doctrine-orm-searchable-repository
```

And just make your repository class extends `SAF\SearchableRepository\SearchableRepository`.

```php

use SAF\SearchableRepository\SearchableRepository;

class MyRepository extends  SearchableRepository
{
    // ...
}
```

Then you can access the `search` method on your repository.

## Filters

This library use filter types to handle how to apply filter condition and ordering. It comes with a GenericType that is the default one but you can add a new filter type.

```php
// add a new type handler to your search with the setType function
// the first parameter correspond to the doctrine type
// the second parameter is the filter type (that must implements \SAF\SearchableRepository\SearchableRepository\Types\TypeInterface interface)
$repository->setType('string', new MyStringFilterType());
```

### GenericType

| Filter   | Supported value | Behavior                                                                              |
|----------|-----------------|---------------------------------------------------------------------------------------|
| eq       | mixed           | Filter elements that match the given value                                            |
| neq      | mixed           | Filter elements that not match the given value                                        |
| lt       | mixed           | Filter elements with value less than the given value                                  |
| lte      | mixed           | Filter elements with value less than or equals the given value                        |
| gt       | mixed           | Filter elements with value greater than the given value                               |
| gte      | mixed           | Filter elements with value greater than or equals the given value                     |
| like     | string          | Use a LIKE statement to filter elements                                               |
| not_like | string          | Use a NOT LIKE statement to filter elements                                           |
| null     | boolean         | true: filter elements with a null value false : filter elements with a non-null value |
| not_null | boolean         | true: filter elements with a non-null value false: filter elements with a null value  |
| in       | array           | Filter elements with value in the given array                                         |
| not_in   | array           | Filter elements with value not in the given array                                     |
![OH Logo](./docs/oh-logo.svg)

OH
==

Another simple yet neat object hydrator library for PHP.


## Install 

```
composer require eddiriarte/oh
```

## Usage

This library allows conversion of data from (nested) array format into proper PHP objects.

```php
// Define your class(es):
class Person
{
    public function __construct(
        private string $firstName,
        private string $lastName
    ) {}
    
    //... here you might define getters/setters 
}

// Initialize hydrator manager:
$manager = new \EddIriarte\Oh\Manager();

// Execute hydration:
$person = $manager->hydrate(Person::class, ['first_name' => 'Bruce', 'last_name' => 'Wayne']);

assert($person instanceof Person);
```

### Array-Key Cases

The default behaviour is to map snake-case keys from array into the class property/parameter names. 
However, whenever required its possible to configure that behaviour in the manager:

```php
use EddIriarte\Oh\Manager;

$snakeCaseManager = new Manager(['source_naming_case' => StringCase::SnakeCase]); // default behaviour
$person1 = $snakeCaseManager->hydrate(Person::class, ['first_name' => 'Bruce', 'last_name' => 'Wayne']);

$camelCaseManager = new Manager(['source_naming_case' => StringCase::CamelCase]);
$person2 = $camelCaseManager->hydrate(Person::class, ['firstName' => 'Bruce', 'lastName' => 'Wayne']);

$studlyCaseManager = new Manager(['source_naming_case' => StringCase::StudlyCase]);
$person3 = $studlyCaseManager->hydrate(Person::class, ['FirstName' => 'Bruce', 'LastName' => 'Wayne']);

$kebabCaseManager = new Manager(['source_naming_case' => StringCase::KebabCase]);
$person4 = $kebabCaseManager->hydrate(Person::class, ['first-name' => 'Bruce', 'last-name' => 'Wayne']);

$anyCaseManager = new Manager(['source_naming_case' => StringCase::AnyCase]);
$person5 = $anyCaseManager->hydrate(Person::class, ['firstName' => 'Bruce', 'last_name' => 'Wayne']);
```

### Data Structure Objects

If for some reason your classes do not provide a respective constructor method, then the hydrator will try to populate matching class properties. 


```php
// Define your class(es):
class Person
{
    private string $firstName;
    private string $lastName;
    
    //... here you might define getters/setters 
}

// Initialize hydrator manager:
$manager = new \EddIriarte\Oh\Manager();

// Execute hydration:
$person = $manager->hydrate(Person::class, ['first_name' => 'Bruce', 'last_name' => 'Wayne']);

assert($person instanceof Person);
```

Additionally, the manager allows to specify the visibility of properties to consider:


```php
use EddIriarte\Oh\Manager;
use EddIriarte\Oh\Enums\PropertyVisibility;

class ReadOnlyPerson
{
    public readonly string $firstName;
    public readonly string $lastName;
    public int $age;
}

// Initialize hydrator manager:
$manager = new Manager(['property_visibility' => PropertyVisibility::ReadOnly]);

// Execute hydration:
$person = $manager->hydrate(ReadOnlyPerson::class, ['first_name' => 'Bruce', 'last_name' => 'Wayne', 'age' => 44]);

var_dump($person);

// class ReadOnlyPerson#700 (3) {
//   public readonly string $firstName =>
//   string(5) "Bruce"
//   public readonly string $lastName =>
//   string(5) "Wayne"
//   public int $age =>
//   *uninitialized*
// }
```

### Nested Objects

The hydrator loops over all properties/parameters and tries to initialize also nested types:

```php
use EddIriarte\Oh\Manager;
use EddIriarte\Oh\Enums\PropertyVisibility;

class Person
{
    public function __construct(
        private string $firstName,
        private string $lastName
    ) {}
}

class Hero
{
    public function __construct(
        private string $name,
        private Person $alias
    ) {}
}

// Execute hydration:
$hero = (new Manager())->hydrate(
    Hero::class,
    [
        'name' => 'Batman',
        'alias' => [
            'first_name' => 'Bruce', 
            'last_name' => 'Wayne',
        ],
    ]
);
```

### List, Dictionaries, Arrays

The hydrator already supports the population of object lists. 
In order to hydrate nested items into specific classes, it requires additional information, that can be provided wit PHP Attributes.

```php
use EddIriarte\Oh\Attributes\ListMemberType;
use EddIriarte\Oh\Enums\PropertyVisibility;
use EddIriarte\Oh\Manager;

class Hero
{
    public function __construct(private string $name)
    {}
}

class HeroTeam
{
    public function __construct(
        private string $name,
        #[ListMemberType(Hero::class)]
        private array $heroes
    ) {}
}

$team = (new Manager())->hydrate(
    HeroTeam::class,
    [
        'name' => 'Batman & Robin',
        'heroes' => [
            ['name' => 'Batman'], 
            ['name' => 'Robin'],
        ],
    ]
);
```

The usage of instantiable Arrayable classes instead of arrays is also allowed. In this example we use Doctrine's
 `ArrayCollection`, but could also be done with Laravel collections:


```php
use Doctrine\Common\Collections\ArrayCollection;

use EddIriarte\Oh\Attributes\ListMemberType;
use EddIriarte\Oh\Enums\PropertyVisibility;
use EddIriarte\Oh\Manager;

class Hero
{
    public function __construct(private string $name)
    {}
}

class HeroTeam
{
    public function __construct(
        private string $name,
        #[ListMemberType(Hero::class)]
        private ArrayCollection $heroes
    ) {}
}

$team = (new Manager())->hydrate(
    HeroTeam::class,
    [
        'name' => 'Batman & Robin',
        'heroes' => [
            ['name' => 'Batman'], 
            ['name' => 'Robin'],
        ],
    ]
);
```

## Configuration

Still in progress...

| Name               | Type                        | Description                                                                                                                                                  |
|--------------------|-----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| source_naming_case | `StringCase` (Enum)         | The case of the keys used in the source array, needed to map them into the specific class property/parameter.<br><br>Default value: `StringCase::SnakeCase`  |
| property_visibility | `PropertyVisibility` (Enum) | The property visibility on structure classes(without constructor) to filter fields.  <br><br>Default value: `PropertyVisibility::Private`                    |


## Credits

String case functions were copied from Laravel.

## Disclaimer

This library is still under active development and usage may change in order to optimize performance, add caching and dehydration. 




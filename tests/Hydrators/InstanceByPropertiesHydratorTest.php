<?php

declare(strict_types=1);

namespace Tests\Oh\Hydrators;

use EddIriarte\Oh\Enums\PropertyVisibility;
use EddIriarte\Oh\Hydrators\InstanceByPropertiesHydrator;
use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Oh\Samples\HeroStruct;
use Tests\Oh\Samples\PrivatePersonStruct;
use Tests\Oh\Samples\PublicPersonStruct;

class InstanceByPropertiesHydratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideTestDataForPersonStruct
     */
    public function it_hydrates_public_person_struct(array $data, Manager $manager, array $expected)
    {
        $builder = new InstanceByPropertiesHydrator(PublicPersonStruct::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(PublicPersonStruct::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->lastName);
        $this->assertEquals($expected['first_name'], $dto->firstName);
    }

    public function provideTestDataForPersonStruct(): Generator
    {
        yield [
            ['first_name' => 'Frank', 'last_name' => 'Sinatra'],
            new Manager([
                'source_naming_case' => StringCase::SnakeCase,
                'property_visibility' => PropertyVisibility::Public,
            ]),
            ['first_name' => 'Frank', 'last_name' => 'Sinatra'],
        ];
        yield [
            ['firstName' => 'Halle', 'lastName' => 'Berry'],
            new Manager([
                'source_naming_case' => StringCase::CamelCase,
                'property_visibility' => PropertyVisibility::Public,
            ]),
            ['first_name' => 'Halle', 'last_name' => 'Berry'],
        ];
        yield [
            ['FirstName' => 'Taylor', 'LastName' => 'Swift'],
            new Manager([
                'source_naming_case' => StringCase::StudlyCase,
                'property_visibility' => PropertyVisibility::Public,
            ]),
            ['first_name' => 'Taylor', 'last_name' => 'Swift'],
        ];
        yield [
            ['first-name' => 'Johnny', 'last-name' => 'Cash'],
            new Manager([
                'source_naming_case' => StringCase::KebabCase,
                'property_visibility' => PropertyVisibility::Public,
            ]),
            ['first_name' => 'Johnny', 'last_name' => 'Cash'],
        ];
    }

    /**
     * @test
     * @dataProvider provideTestDataForPrivatePersonStruct
     */
    public function it_hydrates_private_person_struct(array $data, Manager $manager, array $expected)
    {
        $builder = new InstanceByPropertiesHydrator(PrivatePersonStruct::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(PrivatePersonStruct::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->getLastName());
        $this->assertEquals($expected['first_name'], $dto->getFirstName());
    }

    public function provideTestDataForPrivatePersonStruct(): Generator
    {
        yield [
            ['first_name' => 'Oprah', 'last_name' => 'Winfrey'],
            new Manager([
                'source_naming_case' => StringCase::SnakeCase,
                'property_visibility' => PropertyVisibility::Private,
            ]),
            ['first_name' => 'Oprah', 'last_name' => 'Winfrey'],
        ];
        yield [
            ['firstName' => 'Bruce', 'lastName' => 'Lee'],
            new Manager([
                'source_naming_case' => StringCase::CamelCase,
                'property_visibility' => PropertyVisibility::Private,
            ]),
            ['first_name' => 'Bruce', 'last_name' => 'Lee'],
        ];
        yield [
            ['FirstName' => 'Selena', 'LastName' => 'Gomez'],
            new Manager([
                'source_naming_case' => StringCase::StudlyCase,
                'property_visibility' => PropertyVisibility::Private,
            ]),
            ['first_name' => 'Selena', 'last_name' => 'Gomez'],
        ];
        yield [
            ['first-name' => 'Morgan', 'last-name' => 'Freeman'],
            new Manager([
                'source_naming_case' => StringCase::KebabCase,
                'property_visibility' => PropertyVisibility::Private,
            ]),
            ['first_name' => 'Morgan', 'last_name' => 'Freeman'],
        ];
    }

    /**
     * @test
     * @dataProvider provideTestDataForHeroStruct
     */
    public function it_hydrates_nested_hero_struct(array $data, Manager $manager)
    {
        $builder = new InstanceByPropertiesHydrator(HeroStruct::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(HeroStruct::class, $dto);
        $this->assertEquals($data['name'], $dto->name);
        $this->assertEquals($data['alias']['first_name'], $dto->alias->firstName);
        $this->assertEquals($data['alias']['last_name'], $dto->alias->lastName);
    }

    public function provideTestDataForHeroStruct(): Generator
    {
        yield [
            [
                'name' => 'Batman',
                'psy' => 'NONE',
                'flying' => false,
                'strength' => 75.99,
                'alias' => [
                    'first_name' => 'Bruce',
                    'last_name' => 'Wayne',
                ],
            ],
            new Manager([
                'source_naming_case' => StringCase::SnakeCase,
                'property_visibility' => PropertyVisibility::Public,
            ]),
        ];
    }
}

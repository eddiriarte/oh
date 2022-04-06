<?php

declare(strict_types=1);

namespace Tests\Oh\Builders;

use EddIriarte\Oh\Builders\PlainInstanceBuilder;
use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use Generator;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tests\Oh\Samples\NestedHeroDto;
use Tests\Oh\Samples\PrivatePersonDto;
use Tests\Oh\Samples\PublicPersonDto;

class PlainInstanceBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideTestDataForPersonDto
     */
    public function it_hydrates_public_person_dto(array $data, Manager $manager, array $expected)
    {
        $builder = new PlainInstanceBuilder(PublicPersonDto::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(PublicPersonDto::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->lastName);
        $this->assertEquals($expected['first_name'], $dto->firstName);
    }

    public function provideTestDataForPersonDto(): Generator
    {
        yield [
            ['first_name' => 'Frank', 'last_name' => 'Sinatra'],
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
            ['first_name' => 'Frank', 'last_name' => 'Sinatra'],
        ];
        yield [
            ['firstName' => 'Halle', 'lastName' => 'Berry'],
            new Manager(['source_naming_case' => StringCase::CamelCase]),
            ['first_name' => 'Halle', 'last_name' => 'Berry'],
        ];
        yield [
            ['FirstName' => 'Taylor', 'LastName' => 'Swift'],
            new Manager(['source_naming_case' => StringCase::StudlyCase]),
            ['first_name' => 'Taylor', 'last_name' => 'Swift'],
        ];
        yield [
            ['first-name' => 'Johnny', 'last-name' => 'Cash'],
            new Manager(['source_naming_case' => StringCase::KebabCase]),
            ['first_name' => 'Johnny', 'last_name' => 'Cash'],
        ];
    }

    /**
     * @test
     * @dataProvider provideTestDataForPrivatePersonDto
     */
    public function it_hydrates_private_person_dto(array $data, Manager $manager, array $expected)
    {
        $builder = new PlainInstanceBuilder(PrivatePersonDto::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(PrivatePersonDto::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->getLastName());
        $this->assertEquals($expected['first_name'], $dto->getFirstName());
    }

    public function provideTestDataForPrivatePersonDto(): Generator
    {
        yield [
            ['first_name' => 'Oprah', 'last_name' => 'Winfrey'],
            new Manager([
                'source_naming_case' => StringCase::SnakeCase,
                'property_visibility' => ReflectionProperty::IS_PRIVATE
            ]),
            ['first_name' => 'Oprah', 'last_name' => 'Winfrey'],
        ];
        yield [
            ['firstName' => 'Bruce', 'lastName' => 'Lee'],
            new Manager([
                'source_naming_case' => StringCase::CamelCase,
                'property_visibility' => ReflectionProperty::IS_PRIVATE
            ]),
            ['first_name' => 'Bruce', 'last_name' => 'Lee'],
        ];
        yield [
            ['FirstName' => 'Selena', 'LastName' => 'Gomez'],
            new Manager([
                'source_naming_case' => StringCase::StudlyCase,
                'property_visibility' => ReflectionProperty::IS_PRIVATE
            ]),
            ['first_name' => 'Selena', 'last_name' => 'Gomez'],
        ];
        yield [
            ['first-name' => 'Morgan', 'last-name' => 'Freeman'],
            new Manager([
                'source_naming_case' => StringCase::KebabCase,
                'property_visibility' => ReflectionProperty::IS_PRIVATE
            ]),
            ['first_name' => 'Morgan', 'last_name' => 'Freeman'],
        ];
    }

    /**
     * @test
     * @dataProvider provideTestDataForHeroDto
     */
    public function it_hydrates_nested_hero_dto(array $data, Manager $manager)
    {
        $builder = new PlainInstanceBuilder(NestedHeroDto::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(NestedHeroDto::class, $dto);
        $this->assertEquals($data['name'], $dto->name);
        $this->assertEquals($data['alias']['first_name'], $dto->alias->firstName);
        $this->assertEquals($data['alias']['last_name'], $dto->alias->lastName);
    }

    public function provideTestDataForHeroDto(): Generator
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
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
        ];
    }
}

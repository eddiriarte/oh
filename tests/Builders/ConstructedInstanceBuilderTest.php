<?php

declare(strict_types=1);

namespace Tests\Oh\Builders;

use EddIriarte\Oh\Builders\ConstructedInstanceBuilder;
use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Oh\Samples\ConstructedNestedHeroDto;
use Tests\Oh\Samples\ConstructedPersonDto;
use Tests\Oh\Samples\ConstructedReadOnlyPersonDto;

class ConstructedInstanceBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideTestDataForPersonDto
     */
    public function it_hydrates_constructed_person_dto(array $data, Manager $manager, array $expected)
    {
        $builder = new ConstructedInstanceBuilder(ConstructedPersonDto::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(ConstructedPersonDto::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->getLastName());
        $this->assertEquals($expected['first_name'], $dto->getFirstName());
    }

    /**
     * @test
     * @dataProvider provideTestDataForPersonDto
     */
    public function it_hydrates_constructed_readonly_person_dto(array $data, Manager $manager, array $expected)
    {
        $builder = new ConstructedInstanceBuilder(ConstructedReadOnlyPersonDto::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(ConstructedReadOnlyPersonDto::class, $dto);
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
     * @dataProvider provideTestDataForHeroDto
     */
    public function it_hydrates_nested_hero_dto(array $data, Manager $manager)
    {
        $builder = new ConstructedInstanceBuilder(ConstructedNestedHeroDto::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(ConstructedNestedHeroDto::class, $dto);
        $this->assertEquals($data['name'], $dto->getName());
        $this->assertEquals($data['alias']['first_name'], $dto->getAlias()->getFirstName());
        $this->assertEquals($data['alias']['last_name'], $dto->getAlias()->getLastName());
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
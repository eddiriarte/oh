<?php

declare(strict_types=1);

namespace Tests\Oh\Hydrators;

use EddIriarte\Oh\Hydrators\InstanceByConstructorHydrator;
use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use Generator;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use PHPUnit\Framework\TestCase;
use Tests\Oh\Samples\ArrayedHeroTeamObject;
use Tests\Oh\Samples\HeroTeamObject;
use Tests\Oh\Samples\HeroObject;
use Tests\Oh\Samples\PersonObject;
use Tests\Oh\Samples\ReadOnlyPersonObject;

class InstanceByConstructorHydratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideTestDataForPersonObject
     */
    public function it_hydrates_constructed_person_object(array $data, Manager $manager, array $expected)
    {
        $builder = new InstanceByConstructorHydrator(PersonObject::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(PersonObject::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->getLastName());
        $this->assertEquals($expected['first_name'], $dto->getFirstName());
    }

    /**
     * @test
     * @dataProvider provideTestDataForPersonObject
     */
    public function it_hydrates_constructed_readonly_person_object(array $data, Manager $manager, array $expected)
    {
        $builder = new InstanceByConstructorHydrator(ReadOnlyPersonObject::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(ReadOnlyPersonObject::class, $dto);
        $this->assertEquals($expected['last_name'], $dto->lastName);
        $this->assertEquals($expected['first_name'], $dto->firstName);
    }

    public function provideTestDataForPersonObject(): Generator
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
     * @dataProvider provideTestDataForHeroObject
     */
    public function it_hydrates_nested_hero_object(array $data, Manager $manager, array $expected)
    {
        $builder = new InstanceByConstructorHydrator(HeroObject::class, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf(HeroObject::class, $dto);
        $this->assertEquals($expected['name'], $dto->getName());
        $this->assertEquals($expected['psy'], $dto->getPsy());
        $this->assertEquals($expected['flying'], $dto->isFlying());
        $this->assertEquals($expected['strength'], $dto->getStrength());
        $this->assertEquals($expected['alias']['first_name'], $dto->getAlias()->getFirstName());
        $this->assertEquals($expected['alias']['last_name'], $dto->getAlias()->getLastName());
    }

    public function provideTestDataForHeroObject(): Generator
    {
        yield [
            [
                'name' => 'Superman',
                'strength' => 150,
                'flying' => true,
                'alias' => [
                    'first_name' => 'Clark',
                    'last_name' => 'Kent',
                ],
            ],
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
            [
                'name' => 'Superman',
                'psy' => null,
                'flying' => true,
                'strength' => 150,
                'alias' => [
                    'first_name' => 'Clark',
                    'last_name' => 'Kent',
                ],
            ],
        ];
        yield [
            [
                'name' => 'Batman',
                'strength' => 75.99,
                'alias' => [
                    'first_name' => 'Bruce',
                    'last_name' => 'Wayne',
                ],
            ],
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
            [
                'name' => 'Batman',
                'psy' => null,
                'flying' => false,
                'strength' => 75.99,
                'alias' => [
                    'first_name' => 'Bruce',
                    'last_name' => 'Wayne',
                ],
            ],
        ];
        yield [
            [
                'name' => 'Professor X',
                'psy' => 'Telepathy',
                'strength' => 50,
                'alias' => [
                    'first_name' => 'Charles X.',
                    'last_name' => 'Xavier',
                ],
            ],
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
            [
                'name' => 'Professor X',
                'psy' => 'Telepathy',
                'flying' => false,
                'strength' => 50,
                'alias' => [
                    'first_name' => 'Charles X.',
                    'last_name' => 'Xavier',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideTestDataForHeroTeamObject
     */
    public function it_hydrates_dto_with_list_of_objects(array $data, string $targetClass, Manager $manager, array $expected)
    {
        $builder = new InstanceByConstructorHydrator($targetClass, $manager);

        $dto = $builder->build($data);

        $this->assertInstanceOf($targetClass, $dto);
        $this->assertEquals($expected['name'], $dto->getName());
        $this->assertCount($expected['team_size'], $dto->getHeroes());

        /** @var HeroObject $hero */
        foreach ($dto->getHeroes() as $idx => $hero) {
            $this->assertEquals($expected['heroes'][$idx]['name'], $hero->getName());
        }
    }

    public function provideTestDataForHeroTeamObject(): Generator
    {
        yield [
            [
                'name' => 'Batman & Robin',
                'heroes' => [
                    [
                        'name' => 'Batman',
                        'alias' => [
                            'first_name' => 'Bruce',
                            'last_name' => 'Wayne',
                        ],
                    ],
                    [
                        'name' => 'Robin',
                        'alias' => [
                            'first_name' => 'Dick',
                            'last_name' => 'Grayson',
                        ],
                    ]
                ]
            ],
            HeroTeamObject::class,
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
            [
                'name' => 'Batman & Robin',
                'team_size' => 2,
                'heroes' => [
                    [
                        'name' => 'Batman',
                        'alias' => [
                            'first_name' => 'Bruce',
                            'last_name' => 'Wayne',
                        ],
                    ],
                    [
                        'name' => 'Robin',
                        'alias' => [
                            'first_name' => 'Dick',
                            'last_name' => 'Grayson',
                        ],
                    ]
                ]
            ]
        ];

        yield [
            [
                'name' => 'Justice League',
                'heroes' => [
                    [
                        'name' => 'Batman',
                        'alias' => [
                            'first_name' => 'Bruce',
                            'last_name' => 'Wayne',
                        ],
                    ],
                    [
                        'name' => 'Superman',
                        'alias' => [
                            'first_name' => 'Clark',
                            'last_name' => 'Kent',
                        ],
                    ],
                    [
                        'name' => 'Flash',
                        'alias' => [
                            'first_name' => 'Barry',
                            'last_name' => 'Allen',
                        ],
                    ],
                    [
                        'name' => 'Wonder Woman',
                        'alias' => [
                            'first_name' => 'Diana',
                            'last_name' => '',
                        ],
                    ],
                    [
                        'name' => 'Aquaman',
                        'alias' => [
                            'first_name' => 'Arthur',
                            'last_name' => 'Curry',
                        ],
                    ]
                ]
            ],
            ArrayedHeroTeamObject::class,
            new Manager(['source_naming_case' => StringCase::SnakeCase]),
            [
                'name' => 'Justice League',
                'team_size' => 5,
                'heroes' => [
                    [
                        'name' => 'Batman',
                        'alias' => [
                            'first_name' => 'Bruce',
                            'last_name' => 'Wayne',
                        ],
                    ],
                    [
                        'name' => 'Superman',
                        'alias' => [
                            'first_name' => 'Clark',
                            'last_name' => 'Kent',
                        ],
                    ],
                    [
                        'name' => 'Flash',
                        'alias' => [
                            'first_name' => 'Barry',
                            'last_name' => 'Allen',
                        ],
                    ],
                    [
                        'name' => 'Wonder Woman',
                        'alias' => [
                            'first_name' => 'Diana',
                            'last_name' => '',
                        ],
                    ],
                    [
                        'name' => 'Aquaman',
                        'alias' => [
                            'first_name' => 'Arthur',
                            'last_name' => 'Curry',
                        ],
                    ]
                ]
            ]
        ];
    }

}

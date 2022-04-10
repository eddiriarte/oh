<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

use Doctrine\Common\Collections\ArrayCollection;
use EddIriarte\Oh\Attributes\ListMemberType;

class ArrayedHeroTeamObject
{
    public function __construct(
        private string $name,
        #[ListMemberType(HeroObject::class)] private array $heroes
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getHeroes(): array
    {
        return $this->heroes;
    }
}

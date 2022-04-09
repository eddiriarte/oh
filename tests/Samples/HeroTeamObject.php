<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

use Doctrine\Common\Collections\ArrayCollection;
use EddIriarte\Oh\Attributes\ListMemberType;

class HeroTeamObject
{
    public function __construct(
        private string $name,
        #[ListMemberType(HeroObject::class)] private ArrayCollection $heroes
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
     * @return ArrayCollection
     */
    public function getHeroes(): ArrayCollection
    {
        return $this->heroes;
    }
}

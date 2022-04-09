<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

use Doctrine\Common\Collections\ArrayCollection;
use EddIriarte\Oh\Attributes\ListEntryType;

class HeroTeamObject
{
    public function __construct(
        private string $name,
        #[ListEntryType(HeroObject::class)] private ArrayCollection $heroes
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

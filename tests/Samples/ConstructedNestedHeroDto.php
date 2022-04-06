<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

class ConstructedNestedHeroDto
{
    public function __construct(
        private string $name,
        private ?string $psy = null,
        private bool $flying = false,
        private ?float $strength = 100,
        private ?ConstructedPersonDto $alias = null
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
     * @return string|null
     */
    public function getPsy(): ?string
    {
        return $this->psy;
    }

    /**
     * @return bool
     */
    public function isFlying(): bool
    {
        return $this->flying;
    }

    /**
     * @return float|int|null
     */
    public function getStrength(): float|int|null
    {
        return $this->strength;
    }

    /**
     * @return ConstructedPersonDto|null
     */
    public function getAlias(): ?ConstructedPersonDto
    {
        return $this->alias;
    }
}

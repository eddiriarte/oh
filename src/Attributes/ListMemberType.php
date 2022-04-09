<?php

namespace EddIriarte\Oh\Attributes;

use Attribute;

#[Attribute]
class ListMemberType
{
    public function __construct(public readonly string $type)
    {
    }
}

<?php

namespace EddIriarte\Oh\Attributes;

use Attribute;

#[Attribute]
class ListEntryType
{
    public function __construct(public readonly string $type)
    {
    }
}

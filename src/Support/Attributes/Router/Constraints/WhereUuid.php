<?php

namespace Support\Attributes\Router\Constraints;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class WhereUuid extends Where
{
    public function __construct(public string $name)
    {
        parent::__construct($this->name, '[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}');
    }
}

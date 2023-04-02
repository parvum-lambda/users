<?php

namespace Support\Attributes\Router\Constraints;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class WhereAlphaNumeric extends Where
{
    public function __construct(public string $name)
    {
        parent::__construct($this->name, '[a-zA-Z0-9]+');
    }
}

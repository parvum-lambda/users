<?php

namespace Support\Router\Constraints;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class WhereAlpha extends Where
{
    public function __construct(public string $name)
    {
        parent::__construct($this->name, '[a-zA-Z]+');
    }
}

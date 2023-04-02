<?php

namespace Support\Attributes\Router\Constraints;

use Attribute;
use Support\Attributes\Router\BaseSerializable;

#[Attribute(Attribute::TARGET_METHOD)]
class Where extends BaseSerializable
{
    public function __construct(public string $name, public string $expression)
    {
    }
}

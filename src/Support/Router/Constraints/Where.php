<?php

namespace Support\Router\Constraints;

use Attribute;
use Support\Router\BaseSerializable;

#[Attribute(Attribute::TARGET_METHOD)]
class Where extends BaseSerializable
{
    public function __construct(public string $name, public string $expression)
    {
    }
}

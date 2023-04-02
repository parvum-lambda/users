<?php

namespace Support\Attributes\Router\Options;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class RouteAs extends Option
{
    public string $key = 'as';

    public function __construct(
        public string $as
    ) {
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getValue() : string | array
    {
        return $this->as;
    }
}

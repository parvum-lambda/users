<?php

namespace Support\Router;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class RouteGroup extends BaseSerializable
{
    public function __construct(
        public string $prefix = '',
        public array | string | null $middleware = null,
        public array | string | null $excluded_middleware = null,
        public ?string $as = null,
        public ?string $domain = null
    ) {
    }
}

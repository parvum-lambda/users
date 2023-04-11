<?php

namespace Support\Router;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route extends BaseSerializable
{
    public const GET = 'get';
    public const HEAD = 'head';
    public const OPTIONS = 'options';
    public const POST = 'post';
    public const PUT = 'put';
    public const PATCH = 'patch';
    public const DELETE = 'delete';

    public function __construct(
        public string $method,
        public string $uri = '',
        public array | string | null $middleware = null,
        public array | string | null $excluded_middleware = null,
        public ?string $as = null,
        public ?string $domain = null
    ) {
    }
}

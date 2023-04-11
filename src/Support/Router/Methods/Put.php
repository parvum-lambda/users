<?php

namespace Support\Router\Methods;

use Attribute;
use Support\Router\Route;

#[Attribute(Attribute::TARGET_METHOD)]
final class Put extends Route
{
    public function __construct(
        public string $uri = '',
        public array | string | null $middleware = null,
        public array | string | null $excluded_middleware = null,
        public ?string $as = null,
        public ?string $domain = null
    ) {
        parent::__construct(Route::PUT, $uri, $middleware, $excluded_middleware, $as, $domain);
    }
}

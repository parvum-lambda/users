<?php

namespace Support\Router\Methods;

use Attribute;
use Support\Router\Route;

#[Attribute(Attribute::TARGET_METHOD)]
final class Delete extends Route
{
    public function __construct(
        public string $uri = '',
        public array | string | null $middleware = null,
        public array | string | null $excluded_middleware = null,
        public ?string $as = null,
        public ?string $domain = null
    ) {
        parent::__construct(Route::DELETE, $uri, $middleware, $excluded_middleware, $as, $domain);
    }
}

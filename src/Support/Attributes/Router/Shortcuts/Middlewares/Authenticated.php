<?php

namespace Support\Attributes\Router\Shortcuts\Middlewares;

use App\Api\V1\Auth\CerberusMiddleware;
use Attribute;
use Support\Attributes\Router\Shortcuts\Shortcut;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Authenticated implements Shortcut
{
    public function __construct(private ?string $role = null, private bool $internal = false)
    {
    }

    public function __invoke(array &$options)
    {
        if ($this->role) {
            $middleware = CerberusMiddleware::LABEL . ':' . $this->role;
        } else {
            $middleware = CerberusMiddleware::LABEL;
        }

        $middlewares = [$middleware];

        if ($this->internal) {
            $middlewares[] = 'cerberus-internal-app';
        }

        if (! isset($options['middleware'])) {
            $options['middleware'] = $middlewares;
        } elseif (! is_array($options['middleware'])) {
            $options['middleware'] = [$options['middleware'], ...$middlewares];
        } else {
            $options['middleware'] = [...$options['middleware'], ...$middlewares];
        }
    }
}

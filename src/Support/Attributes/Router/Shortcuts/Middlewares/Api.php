<?php

namespace Support\Attributes\Router\Shortcuts\Middlewares;

use Attribute;
use Support\Attributes\Router\Shortcuts\Shortcut;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Api implements Shortcut
{
    public function __construct(private string $version = '')
    {
        if ($version) {
            $this->version = '/' . $version;
        }
    }

    public function __invoke(array &$options)
    {
        if (! isset($options['prefix'])) {
            $options['prefix'] = '/api' . $this->version;
        } elseif (! str_starts_with($options['prefix'], '/')) {
            $options['prefix'] = '/api' . $this->version . '/' . $options['prefix'];
        }

        $middlewares = ['api'];

        if (! isset($options['middleware'])) {
            $options['middleware'] = $middlewares;
        } elseif (! is_array($options['middleware'])) {
            $options['middleware'] = [$options['middleware'], ...$middlewares];
        } else {
            $options['middleware'] = [...$options['middleware'], ...$middlewares];
        }
    }
}

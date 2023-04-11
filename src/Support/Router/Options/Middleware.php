<?php

namespace Support\Router\Options;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Middleware extends Option
{
    public string $key;
    public array $value;

    public function __construct(
        array | string $middleware
    ) {
        $this->key = 'middleware';

        if (empty($middleware)) {
            throw new InvalidArgumentException('Middleware can\'t be empty');
        }

        if (is_array($middleware)) {
            $this->value = $middleware;
        } else {
            $this->value = [$middleware];
        }
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getValue() : string | array
    {
        return $this->value;
    }
}

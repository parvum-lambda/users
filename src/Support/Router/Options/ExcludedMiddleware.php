<?php

namespace Support\Router\Options;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class ExcludedMiddleware extends Option
{
    private string $key = 'excluded_middleware';
    private array $value;

    public function __construct(
        public array | string $excludedMiddleware
    ) {
        if (empty($excludedMiddleware)) {
            throw new InvalidArgumentException('Excluded Middlewares can\'t be empty');
        }

        if (is_array($excludedMiddleware)) {
            $this->value = $excludedMiddleware;
        } else {
            $this->value = [$excludedMiddleware];
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

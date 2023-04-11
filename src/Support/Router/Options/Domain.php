<?php

namespace Support\Router\Options;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Domain extends Option
{
    private string $key;

    public function __construct(
        public string $domain
    ) {
        $this->key = 'domain';

        if (! $domain) {
            throw new InvalidArgumentException('domain can\'t be empty');
        }
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getValue() : string | array
    {
        return $this->domain;
    }
}

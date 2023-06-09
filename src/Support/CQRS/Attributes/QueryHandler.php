<?php

namespace Support\CQRS\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class QueryHandler
{
    /**
     * @param class-string $targetQueryClass
     */
    public function __construct(private string $targetQueryClass)
    {
    }

    /**
     * @return string
     */
    public function getTargetQueryClass() : string
    {
        return $this->targetQueryClass;
    }
}

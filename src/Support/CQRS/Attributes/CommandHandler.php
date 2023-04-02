<?php

namespace Support\CQRS\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class CommandHandler
{
    /**
     * @param class-string $targetCommandClass
     */
    public function __construct(private string $targetCommandClass)
    {
    }

    /**
     * @return string
     */
    public function getTargetCommandClass() : string
    {
        return $this->targetCommandClass;
    }
}

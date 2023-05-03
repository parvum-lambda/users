<?php

namespace Support\CQRS\Attributes;

use Attribute;
use Support\CQRS\Interfaces\KafkaTopicsSet;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class EventProducer
{
    public function __construct(private KafkaTopicsSet $topic)
    {
    }

    /**
     * @return string
     */
    public function getTopic() : string
    {
        return $this->topic->getTopic();
    }
}
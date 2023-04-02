<?php

namespace Support\CQRS\Interfaces;

interface KafkaTopicsSet
{
    public function getTopic() : string;
}

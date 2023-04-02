<?php

namespace Domain\Users\Events\Producers;

use Domain\Users\Events\Struct\UserCreatedEventStruct;
use Domain\Users\UserEvents;
use Support\CQRS\Attributes\EventProducer;
use Support\CQRS\EventProducer as EventProducerBase;
use Support\CQRS\Interfaces\DataSet;

#[EventProducer(UserEvents::CREATE_USER)]
class CreateUserEventProducer extends EventProducerBase
{
    /**
     * @param UserCreatedEventStruct $userCreatedEvent
     */
    public function __construct(private readonly DataSet $userCreatedEvent)
    {
    }

    public function getDataSet() : DataSet
    {
        return $this->userCreatedEvent;
    }
}

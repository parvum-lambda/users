<?php

namespace Domain\Users\Events\Consumers;

use Domain\Users\Events\Struct\UserCreatedEventStruct;
use Domain\Users\UserEvents;
use Support\CQRS\Attributes\EventConsumer;
use Support\CQRS\Interfaces\DataSet;
use Support\CQRS\Interfaces\EventConsumer as EventConsumerBase;

#[EventConsumer(UserEvents::CREATE_USER)]
class CreateUserEventConsumer implements EventConsumerBase
{
    /**
     * @param UserCreatedEventStruct $dataSet
     */
    public function handle(DataSet $dataSet)
    {
        logger('CreateUserEventConsumer');
        logger(json_encode($dataSet->getData()));
    }
}

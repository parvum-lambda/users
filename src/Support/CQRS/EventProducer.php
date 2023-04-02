<?php

namespace Support\CQRS;

use Exception;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use ReflectionClass;
use Support\CQRS\Attributes\EventProducer as EventProducerAttribute;
use Support\CQRS\Interfaces\DataSet;

abstract class EventProducer
{
    abstract public function __construct(DataSet $dataSet);

    abstract public function getDataSet() : DataSet;

    /**
     * @throws Exception
     */
    public function dispatch() : void
    {
        $reflector = new ReflectionClass($this::class);
        $attributes = $reflector->getAttributes(EventProducerAttribute::class);

        if (empty($attributes)) {
            throw new Exception('Topic not defined');
        }

        $eventProducerAttribute = $attributes[0]->newInstance();

        assert($eventProducerAttribute instanceof EventProducerAttribute);

        $kafkaProducer = Kafka::publishOn($eventProducerAttribute->getTopic());

        $kafkaProducer->withMessage(
            new Message(
                headers: [
                    'topic' => $eventProducerAttribute->getTopic(),
                ],
                body: $this->getDataSet()->getData(),
            )
        );

        $kafkaProducer->send();
    }
}

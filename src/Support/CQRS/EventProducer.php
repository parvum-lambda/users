<?php

namespace Support\CQRS;

use Exception;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;
use Support\CQRS\Attributes\EventProducer as EventProducerAttribute;
use Support\CQRS\Interfaces\DataSet;
use Support\CQRS\Interfaces\PersistedEvent;

abstract class EventProducer
{
    abstract public function __construct(DataSet $dataSet);

    abstract public function getDataSet() : DataSet;

    private function getEntityId() : UuidInterface
    {
        return $this->getDataSet()->getData()['id'];
    }

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

        $topic = $eventProducerAttribute->getTopic();
        $eventData = $this->getDataSet()->getData();
        $entityId = $this->getEntityId();

        $persistedEvent = $this->persistEvent($topic, $entityId, $eventData);

        $kafkaProducer = Kafka::publishOn($eventProducerAttribute->getTopic());

        $kafkaProducer->withMessage(
            new Message(
                headers: [
                    'topic' => $topic,
                ],
                body: $eventData,
            )
        );

        $kafkaProducer->send();

        $persistedEvent->flagAsPublished();
    }

    /**
     * @throws Exception
     */
    private function persistEvent(string $topic, UuidInterface $entityId, mixed $eventData) : PersistedEvent
    {
        $eventPersistencyClassName = config('cqrs.event_persistency_class');

        if (! $eventPersistencyClassName) {
            throw new Exception('Event persistency class not defined');
        }

        assert(is_a($eventPersistencyClassName, PersistedEvent::class));

        $eventPersistencyClass = $eventPersistencyClassName::persistEvent($topic, $entityId, $eventData);

        assert($eventPersistencyClass instanceof PersistedEvent);

        return $eventPersistencyClass;
    }
}

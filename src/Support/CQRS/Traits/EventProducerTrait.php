<?php

namespace Support\CQRS\Traits;

use Exception;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use ReflectionClass;
use Support\CQRS\Attributes\EventProducer;

trait EventProducerTrait
{
    /**
     * @throws Exception
     */
    public function publish() : void
    {
        $reflector = new ReflectionClass(self::class);
        $attributes = $reflector->getAttributes(EventProducer::class);

        if (empty($attributes)) {
            throw new Exception('Topic not defined');
        }

        $eventProducerAttribute = $attributes[0]->newInstance();

        assert($eventProducerAttribute instanceof EventProducer);

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

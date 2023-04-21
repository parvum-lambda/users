<?php

namespace Support\CQRS;

use Aws\DynamoDb\BinaryValue;
use Exception;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\UuidInterface;
use Support\CQRS\Facades\CQRSService;
use Support\CQRS\Interfaces\DataSet;
use Support\CQRS\Interfaces\PersistedEvent;

class DynamoDBPersistedEvent implements PersistedEvent
{
    private const ENTITY_ID_KEY = 'entityId';
    private DynamoDbEventModel $model;
    private function __construct(
        private readonly UuidInterface $id,
        private readonly string $topic,
        private readonly UuidInterface $entityId,
        private readonly mixed $eventData,
    ) {
    }

    public static function find(mixed $id) : PersistedEvent
    {
        $event = DynamoDbEventModel::find($id);

        return self::formatPersistedEvent($event);
    }

    /**
     * @param mixed $entityId
     * @return Collection
     */
    public static function findByEntity(mixed $entityId) : Collection
    {
        $events = DynamoDbEventModel::where(self::ENTITY_ID_KEY, $entityId)->get();

        assert($events instanceof Collection);

        return $events->map(static function (DynamoDbEventModel $event) {
            return self::formatPersistedEvent($event);
        });
    }

    private static function formatPersistedEvent(DynamoDbEventModel $event) : PersistedEvent
    {
        $struct = CQRSService::getStructForEventTopic($event->topic);

        if ($struct) {
            $eventData = new $struct(...json_decode($event->eventData));
        } else {
            $eventData = $event->eventData;
        }

        $persistedEvent = new self(
            $event->id,
            $event->topic,
            $event->entityId,
            $eventData,
        );

        $persistedEvent->model = $event;

        return $persistedEvent;
    }

    /**
     * @param string $topic
     * @param mixed $entityId
     * @param mixed $eventData
     * @return PersistedEvent
     * @throws Exception
     */
    public static function persistEvent(string $topic, UuidInterface $entityId, mixed $eventData) : PersistedEvent
    {
        if (is_array($eventData)) {
            $eventDataBuffer = $eventData;
        } elseif ($eventData instanceof DataSet) {
            $eventDataBuffer = $eventData->getData();
        }

        if (! isset($eventDataBuffer)) {
            throw new Exception('Invalid event data');
        }

        $event = DynamoDbEventModel::create([
            'topic'     => $topic,
            'entityId'  => new BinaryValue($entityId->getBytes()),
            'eventData' => json_encode($eventDataBuffer),
        ]);

        $binaryId = $event->getAttribute('id');

        assert($binaryId instanceof BinaryValue);

        $persistedEvent = new self(
            UuidV6::fromBytes($binaryId),
            $topic,
            $entityId,
            $eventData,
        );

        $persistedEvent->model = $event;

        return $persistedEvent;
    }

    public function getId() : UuidInterface
    {
        return $this->id;
    }

    public function getTopic() : string
    {
        return $this->topic;
    }

    public function getEntityId() : UuidInterface
    {
        return $this->entityId;
    }

    public function getEventData() : mixed
    {
        $struct = CQRSService::getStructForEventTopic($this->topic);

        if ($struct) {
            $eventData = new $struct(...json_decode($this->eventData));
        } else {
            $eventData = $this->eventData;
        }

        return $eventData;
    }

    public function flagAsPublished() : PersistedEvent
    {
        $this->model
            ->setPublished(true)
            ->save();

        return $this;
    }
}

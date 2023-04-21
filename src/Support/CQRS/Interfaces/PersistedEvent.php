<?php

namespace Support\CQRS\Interfaces;

use Ramsey\Uuid\UuidInterface;

interface PersistedEvent
{
    public static function find(mixed $id) : PersistedEvent;

    public static function persistEvent(string $topic, UuidInterface $entityId, mixed $eventData) : PersistedEvent;
    public function getId() : mixed;

    public function getEntityId() : mixed;

    public function getEventData() : mixed;
    public function flagAsPublished() : self;
}

<?php

namespace Domain\Users\Events\Struct;

use Domain\Users\UserEvents;
use Ramsey\Uuid\UuidInterface;
use Support\CQRS\Attributes\EventStruct;
use Support\CQRS\Interfaces\DataSet;

#[EventStruct(UserEvents::CREATE_USER)]
readonly class UserCreatedEventStruct implements DataSet
{
    private array $userData;
    public function __construct(
        UuidInterface $id,
        string $name,
        string $email,
        string $document,
    ) {
        $this->userData = [
            'id'       => $id,
            'name'     => $name,
            'email'    => $email,
            'document' => $document,
        ];
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->userData['name'];
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->userData['email'];
    }

    /**
     * @return string
     */
    public function getDocument() : string
    {
        return $this->userData['document'];
    }

    public function getData() : array
    {
        return $this->userData;
    }
}

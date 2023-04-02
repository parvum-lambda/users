<?php

namespace Domain\Users;

use Support\CQRS\Interfaces\KafkaTopicsSet;

enum UserEvents : string implements KafkaTopicsSet
{
    case CREATE_USER = 'parvum.users.UserCreated';

    public function getTopic() : string
    {
        return $this->value;
    }
}

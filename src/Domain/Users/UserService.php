<?php

namespace Domain\Users;

use Domain\Users\Models\User;
use Ramsey\Uuid\UuidInterface;

class UserService
{
    public function createUser(UuidInterface $id, string $name, string $email, string $document) : void
    {
        $user = new User();
        $user->id = $id->toString();
        $user->name = $name;
        $user->email = $email;
        $user->document = $document;
        $user->save();
    }
}

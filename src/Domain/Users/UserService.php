<?php

namespace Domain\Users;

use Domain\Users\Models\User;
use Domain\Users\Queries\Exceptions\UserNotFoundException;
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

    /**
     * @param string $id
     *
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function getUser(string $id) : User
    {
        $user = User::find($id);

        if (! $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}

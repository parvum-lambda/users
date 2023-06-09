<?php

namespace Domain\Users\Queries\Exceptions;

use App\Exceptions\BaseUsersException;
use App\Exceptions\UsersExceptions;

class UserNotFoundException extends BaseUsersException
{
    public function __construct()
    {
        parent::__construct(UsersExceptions::USER_NOT_FOUND);
    }
}

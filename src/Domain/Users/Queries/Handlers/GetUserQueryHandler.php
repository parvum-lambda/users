<?php

namespace Domain\Users\Queries\Handlers;

use Domain\Users\Models\User;
use Domain\Users\Queries\Struct\GetUserQuery;
use Domain\Users\UserService;
use Exception;
use Support\CQRS\Attributes\QueryHandler;
use Support\CQRS\Interfaces\CommandHandler as CommandHandlerInterface;

#[QueryHandler(GetUserQuery::class)]
class GetUserQueryHandler implements CommandHandlerInterface
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param GetUserQuery $data
     *
     * @return User
     * @throws Exception
     */
    public function handle($data) : User
    {
        return $this->userService->getUser($data->getId());
    }
}

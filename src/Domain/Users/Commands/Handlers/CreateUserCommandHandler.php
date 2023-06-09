<?php

namespace Domain\Users\Commands\Handlers;

use Domain\Users\Commands\Struct\CreateUserCommand;
use Domain\Users\Events\Producers\CreateUserEventProducer;
use Domain\Users\Events\Struct\UserCreatedEventStruct;
use Domain\Users\UserService;
use Exception;
use Ramsey\Uuid\Rfc4122\UuidV7;
use Support\CQRS\Attributes\CommandHandler;
use Support\CQRS\Interfaces\CommandHandler as CommandHandlerInterface;

#[CommandHandler(CreateUserCommand::class)]
class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param CreateUserCommand $data
     *
     * @return mixed
     * @throws Exception
     */
    public function handle($data) : mixed
    {
        $userId = UuidV7::uuid7();

        (new CreateUserEventProducer(
            new UserCreatedEventStruct(
                UuidV7::uuid7(),
                ...$data->getData(),
            )
        ))->dispatch();

        $this->userService->createUser($userId, ...$data->getData());

        return null;
    }
}

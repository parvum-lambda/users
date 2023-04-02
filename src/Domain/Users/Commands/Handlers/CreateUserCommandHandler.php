<?php

namespace Domain\Users\Commands\Handlers;

use Domain\Users\Commands\Struct\CreateUserCommand;
use Domain\Users\Events\Producers\CreateUserEventProducer;
use Domain\Users\Events\Struct\UserCreatedEventStruct;
use Exception;
use Support\CQRS\Attributes\CommandHandler;
use Support\CQRS\Interfaces\CommandHandler as CommandHandlerInterface;

#[CommandHandler(CreateUserCommand::class)]
class CreateUserCommandHandler implements CommandHandlerInterface
{
    /**
     * @param CreateUserCommand $data
     *
     * @return mixed
     * @throws Exception
     */
    public function handle($data) : mixed
    {
        (new CreateUserEventProducer(
            new UserCreatedEventStruct(
                ...$data->getData()
            )
        ))->dispatch();

        return null;
    }
}

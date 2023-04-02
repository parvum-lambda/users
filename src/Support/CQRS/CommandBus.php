<?php

namespace Support\CQRS;

use Support\CQRS\Interfaces\CommandHandler;

readonly class CommandBus
{
    public function __construct(private CQRSService $CQRSService)
    {
    }

    public function execute($command) : mixed
    {
        $handlerClass = $this->CQRSService->getHandlerFromCommand(get_class($command));

        $handler = app($handlerClass);

        assert($handler instanceof CommandHandler);

        return $handler->handle($command);
    }
}

<?php

namespace Support\CQRS;

readonly class CommandBus
{
    public function __construct(private CQRSService $CQRSService)
    {
    }

    public function execute($command) : mixed
    {
        $handler = $this->CQRSService->getHandlerForCommand(get_class($command));

        return $handler?->handle($command);
    }
}

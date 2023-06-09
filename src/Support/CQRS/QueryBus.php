<?php

namespace Support\CQRS;

readonly class QueryBus
{
    public function __construct(private CQRSService $CQRSService)
    {
    }

    public function execute($command) : mixed
    {
        $handler = $this->CQRSService->getHandlerForQuery(get_class($command));

        return $handler?->handle($command);
    }
}

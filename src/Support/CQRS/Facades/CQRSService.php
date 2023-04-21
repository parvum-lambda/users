<?php

namespace Support\CQRS\Facades;

use Illuminate\Support\Facades\Facade;
use Support\CQRS\CQRSService as CQRSServiceRoot;
use Support\CQRS\Interfaces\CommandHandler;
use Support\CQRS\Interfaces\EventConsumer;

/**
 * @method static null init()
 * @method static CommandHandler| null getHandlerForCommand(string $commandClass)
 * @method static EventConsumer[] getHandlersForTopic(string $topic)
 * @method static mixed getStructForEventTopic(string $topic)
 */
class CQRSService extends Facade
{
    public static function getFacadeAccessor() : string
    {
        return CQRSServiceRoot::class;
    }
}

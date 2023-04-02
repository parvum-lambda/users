<?php

namespace Support\CQRS\Facades;

use Illuminate\Support\Facades\Facade;
use Support\CQRS\CQRSService as CQRSServiceRoot;

/**
 * @method static null init()
 * @method static string|null getHandlerFromCommand(string $commandClass)
 */
class CQRSService extends Facade
{
    public static function getFacadeAccessor() : string
    {
        return CQRSServiceRoot::class;
    }
}

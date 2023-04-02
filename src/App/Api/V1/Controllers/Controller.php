<?php

namespace App\Api\V1\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Support\Attributes\Router\RouteGroup;
use Support\Attributes\Router\Shortcuts\Middlewares\Api;

#[RouteGroup]
#[Api('v1')]
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

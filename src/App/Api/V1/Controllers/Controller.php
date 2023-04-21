<?php

namespace App\Api\V1\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Support\Router\Options\Middleware;
use Support\Router\RouteGroup;
use Support\Router\Shortcuts\Middlewares\Api;

#[RouteGroup]
#[Api('v1')]
#[Middleware('json')]
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

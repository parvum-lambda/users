<?php

namespace App\Api\V1\Controllers;

use Domain\Users\Commands\Struct\CreateUserCommand;
use Illuminate\Http\Request;
use Support\CQRS\CommandBus;
use Support\Router\Methods\Post;
use Support\Router\RouteGroup;

#[RouteGroup('users')]
class UsersController extends Controller
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    #[Post]
    public function create(Request $request) : mixed
    {
        return $this->commandBus->execute(new CreateUserCommand(...$request->all()));
    }
}

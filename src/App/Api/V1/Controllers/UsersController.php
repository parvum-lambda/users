<?php

namespace App\Api\V1\Controllers;

use Domain\Users\Commands\Struct\CreateUserCommand;
use Illuminate\Http\Request;
use Support\Attributes\Router\Methods\Post;
use Support\Attributes\Router\RouteGroup;
use Support\CQRS\CommandBus;

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

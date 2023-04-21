<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UsersRequest;
use Domain\Users\Commands\Struct\CreateUserCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Support\CQRS\CommandBus;
use Support\Router\Methods\Post;
use Support\Router\RouteGroup;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[RouteGroup('users')]
class UsersController extends Controller
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    #[Post]
    public function store(UsersRequest $request) : JsonResponse
    {
        $user = $this->commandBus->execute(
            new CreateUserCommand(
                ...$request->all()
            )
        );

        return ResponseFacade::json($user, HttpResponse::HTTP_CREATED);
    }
}

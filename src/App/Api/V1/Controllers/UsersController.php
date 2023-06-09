<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\UsersRequest;
use Domain\Users\Commands\Struct\CreateUserCommand;
use Domain\Users\Queries\Struct\GetUserQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Support\CQRS\CommandBus;
use Support\CQRS\QueryBus;
use Support\Router\Methods\Get;
use Support\Router\Methods\Post;
use Support\Router\RouteGroup;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[RouteGroup('users')]
class UsersController extends Controller
{
    public function __construct(private readonly CommandBus $commandBus, private readonly QueryBus $queryBus)
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

    #[Get('{userId}')]
    public function get(string $userId) : JsonResponse
    {
        $user = $this->commandBus->execute(
            new GetUserQuery($userId)
        );

        return ResponseFacade::json($user, HttpResponse::HTTP_OK);
    }
}

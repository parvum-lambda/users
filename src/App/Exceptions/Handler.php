<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register() : void
    {
        $this->renderable(static function (BaseUsersException $e) {
            $responseBody = [
                'code'        => $e->getCode(),
                'code_string' => $e->getCodeStr(),
                'message'     => $e->getMessage(),
            ];

            if (config('app.debug') === true) {
                $responseBody['file'] = $e->getFile();
                $responseBody['line'] = $e->getLine();
                $responseBody['exception'] = get_class($e);
                $responseBody['trace'] = $e->getTrace();
            }

            return Response::json($responseBody, $e->getHttpStatusCode());
        });
    }

    public function report(Throwable $e) : void
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }
}

<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

enum UsersExceptions : int
{
    public const DEFAULT_ERROR_KEY = 0;

    case ERROR_NOT_MAPPED = 100000;

    case USER_NOT_FOUND = 200001;

    private const ERRORS_MAP = [
        [
            'code'             => UsersExceptions::ERROR_NOT_MAPPED,
            'message'          => 'Error not mapped, contact the developer',
            'http_status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ],
        [
            'code'             => UsersExceptions::USER_NOT_FOUND,
            'message'          => 'User not found',
            'http_status_code' => Response::HTTP_NOT_FOUND,
        ],
    ];

    public static function getErrorFromCode(UsersExceptions $code) : array
    {
        foreach (self::ERRORS_MAP as $error) {
            if ($error['code'] === $code) {
                return $error;
            }
        }

        return self::ERRORS_MAP[self::DEFAULT_ERROR_KEY];
    }
}

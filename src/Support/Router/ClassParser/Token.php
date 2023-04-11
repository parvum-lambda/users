<?php

namespace Support\Router\ClassParser;

readonly class Token
{
    public string $token;

    public function __construct(public int $id, string $token, public int $line)
    {
        $this->token = trim($token);
    }
}

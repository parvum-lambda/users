<?php

namespace Support\CQRS\Interfaces;

interface QueryHandler
{
    public function handle(mixed $data) : mixed;
}

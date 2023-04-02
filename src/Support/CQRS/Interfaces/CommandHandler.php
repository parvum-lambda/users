<?php

namespace Support\CQRS\Interfaces;

interface CommandHandler
{
    public function handle(mixed $data) : mixed;
}

<?php

namespace Support\Attributes\Router\Shortcuts;

interface Shortcut
{
    public function __invoke(array &$options);
}

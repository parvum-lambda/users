<?php

namespace Support\Router\Shortcuts;

interface Shortcut
{
    public function __invoke(array &$options);
}

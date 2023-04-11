<?php

use Support\Router\ClassParser\ClassParser;

function getClasses($path = '') : Generator
{
    $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($path), FilesystemIterator::SKIP_DOTS));
    $regexIterator = new RegexIterator($directoryIterator, '/\.php$/');

    foreach ($regexIterator as $phpFile) {
        $path = $phpFile->getRealPath();

        if (! is_file($path)) {
            continue;
        }

        $classParser = new ClassParser(file_get_contents($path));

        foreach ($classParser->getClasses() as $class) {
            yield $class;
        }
    }

    $directoryIterator->endIteration();
}

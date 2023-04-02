<?php

namespace Support\CQRS;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RegexIterator;
use Support\Attributes\Router\ClassParser\ClassParser;
use Support\CQRS\Attributes\CommandHandler;
use Support\CQRS\Attributes\EventHandler;

class CQRSService
{
    private array $commandHandlerMap = [];
    private array $eventHandlerMap = [];

    /**
     * @return void
     * @throws ReflectionException
     */
    public function init() : void
    {
        $domainClasses = $this->getClasses();

        $this->createMappings($domainClasses);
    }

    public function getHandlerFromCommand(string $commandStructClass)
    {
        return $this->commandHandlerMap[$commandStructClass] ?? null;
    }

    /**
     * @param Generator|string[] $classes
     * @return void
     *
     * @throws ReflectionException
     */
    private function createMappings(array|Generator $classes) : void
    {
        $commandHandlerMap = [];
        $eventHandlerMap = [];
        foreach ($classes as $fullClass) {
            $class = new ReflectionClass($fullClass);

            if ($class->isAbstract()) {
                continue;
            }

            self::pushToCommandHandlerMap($commandHandlerMap, $class) || self::pushToEventHandlerMap($eventHandlerMap, $class);
        }

        $this->commandHandlerMap = $commandHandlerMap;
        $this->eventHandlerMap = $eventHandlerMap;
    }

    private static function pushToCommandHandlerMap(&$stack, ReflectionClass $currentClass) : bool
    {
        $commandHandlerAttributeBuffer = $currentClass->getAttributes(CommandHandler::class);

        if (empty($commandHandlerAttributeBuffer)) {
            return false;
        }

        $commandHandlerAttribute = $commandHandlerAttributeBuffer[0];

        $commandHandlerAttributeInstance = $commandHandlerAttribute->newInstance();

        assert($commandHandlerAttributeInstance instanceof CommandHandler);

        $stack[$commandHandlerAttributeInstance->getTargetCommandClass()] = $currentClass->getName();

        return true;
    }

    private static function pushToEventHandlerMap(&$stack, ReflectionClass $currentClass) : bool
    {
        $eventHandlerAttributeBuffer = $currentClass->getAttributes(EventHandler::class);

        if (empty($eventHandlerAttributeBuffer)) {
            return false;
        }

        $eventHandlerAttribute = $eventHandlerAttributeBuffer[0];

        $eventHandlerAttributeInstance = $eventHandlerAttribute->newInstance();

        assert($eventHandlerAttributeInstance instanceof EventHandler);

        $stack[$eventHandlerAttributeInstance->getTargetEventClass()] = $currentClass->getName();

        return true;
    }

    /**
     * @return Generator
     */
    private function getClasses() : Generator
    {
        $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('src/Domain'), FilesystemIterator::SKIP_DOTS));
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
}

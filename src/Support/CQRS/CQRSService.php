<?php

namespace Support\CQRS;

use Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use Support\CQRS\Attributes\CommandHandler;
use Support\CQRS\Attributes\EventConsumer;

class CQRSService
{
    private const DOMAIN_PATH = 'src/Domain';
    private array $commandHandlerMap = [];
    private array $eventHandlerMap = [];
    private array $topicList = [];

    /**
     * @return void
     * @throws ReflectionException
     */
    public function init() : void
    {
        $domainClasses = getClasses(self::DOMAIN_PATH);

        $this->createMappings($domainClasses);
    }

    /**
     * @param string $commandStructClass
     * @return Interfaces\CommandHandler|null
     */
    public function getHandlerForCommand(string $commandStructClass) : Interfaces\CommandHandler| null
    {
        return $this->commandHandlerMap[$commandStructClass] ?? null;
    }

    /**
     * @param string $topic
     * @return Interfaces\EventConsumer[]
     */
    public function getHandlersForTopic(string $topic) : array
    {
        return $this->eventHandlerMap[$topic] ?? [];
    }

    /**
     * @return string[]
     */
    public function getTopics() : array
    {
        return $this->topicList;
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
        $topicList = [];

        foreach ($classes as $fullClass) {
            $class = new ReflectionClass($fullClass);

            if ($class->isAbstract()) {
                continue;
            }

            self::pushToCommandHandlerMap($commandHandlerMap, $class) || self::pushToEventHandlerMap($eventHandlerMap, $topicList, $class);
        }

        $this->commandHandlerMap = $commandHandlerMap;
        $this->eventHandlerMap = $eventHandlerMap;
        $this->topicList = $topicList;
    }

    /**
     * @param $stack
     * @param ReflectionClass $currentClass
     * @return bool
     * @throws BindingResolutionException
     */
    private static function pushToCommandHandlerMap(&$stack, ReflectionClass $currentClass) : bool
    {
        $commandHandlerAttributeBuffer = $currentClass->getAttributes(CommandHandler::class);

        if (empty($commandHandlerAttributeBuffer)) {
            return false;
        }

        $commandHandlerAttribute = $commandHandlerAttributeBuffer[0];

        $commandHandlerAttributeInstance = $commandHandlerAttribute->newInstance();

        assert($commandHandlerAttributeInstance instanceof CommandHandler);

        $stack[$commandHandlerAttributeInstance->getTargetCommandClass()] = Container::getInstance()->make($currentClass->getName());

        return true;
    }

    /**
     * @throws BindingResolutionException
     */
    private static function pushToEventHandlerMap(&$eventHandlerStack, &$topicStack, ReflectionClass $currentClass) : bool
    {
        $eventHandlerAttributeBuffer = $currentClass->getAttributes(EventConsumer::class);

        if (empty($eventHandlerAttributeBuffer)) {
            return false;
        }

        $eventHandlerAttribute = $eventHandlerAttributeBuffer[0];

        $eventHandlerAttributeInstance = $eventHandlerAttribute->newInstance();

        assert($eventHandlerAttributeInstance instanceof EventConsumer);

        $topic = $eventHandlerAttributeInstance->getTopic();

        if (! in_array($topic, $topicStack)) {
            $topicStack[] = $topic;
        }

        if (! isset($eventHandlerStack[$topic])) {
            $eventHandlerStack[$topic] = [];
        }

        $eventHandlerStack[$topic][] = Container::getInstance()->make(($currentClass->getName()));

        return true;
    }
}

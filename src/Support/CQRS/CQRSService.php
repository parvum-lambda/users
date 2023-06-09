<?php

namespace Support\CQRS;

use Exception;
use Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use Support\CQRS\Attributes\CommandHandler;
use Support\CQRS\Attributes\EventConsumer;
use Support\CQRS\Attributes\EventStruct;
use Support\CQRS\Attributes\QueryHandler;

class CQRSService
{
    private const DOMAIN_PATH = 'src/Domain';
    private array $commandHandlerMap = [];
    private array $queryHandlerMap = [];
    private array $eventHandlerMap = [];
    private array $eventTopicStructMap = [];
    private array $topicList = [];

    /**
     * @return void
     * @throws ReflectionException
     * @throws BindingResolutionException
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
     * @param string $queryStructClass
     * @return Interfaces\CommandHandler|null
     */
    public function getHandlerForQuery(string $queryStructClass) : Interfaces\QueryHandler| null
    {
        return $this->queryHandlerMap[$queryStructClass] ?? null;
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
     * @param string $topic
     * @return mixed
     */
    public function getStructForEventTopic(string $topic) : mixed
    {
        return $this->eventTopicStructMap[$topic] ?? null;
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
     * @throws ReflectionException|]
     * @throws BindingResolutionException|]
     */
    private function createMappings(array|Generator $classes) : void
    {
        $commandHandlerMap = [];
        $eventHandlerMap = [];
        $eventTopicStructMap = [];
        $topicList = [];

        foreach ($classes as $fullClass) {
            $class = new ReflectionClass($fullClass);

            if ($class->isAbstract()) {
                continue;
            }

            self::pushToCommandHandlerMap($commandHandlerMap, $class) ||
            self::pushToQueryHandlerMap($commandHandlerMap, $class) ||
            self::pushToEventHandlerMap($eventHandlerMap, $topicList, $class) ||
            self::pushToTopicStructMap($eventTopicStructMap, $class);
        }

        $this->commandHandlerMap = $commandHandlerMap;
        $this->eventHandlerMap = $eventHandlerMap;
        $this->topicList = $topicList;
        $this->eventTopicStructMap = $eventTopicStructMap;
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
     * @param $stack
     * @param ReflectionClass $currentClass
     * @return bool
     * @throws BindingResolutionException
     */
    private static function pushToQueryHandlerMap(&$stack, ReflectionClass $currentClass) : bool
    {
        $queryHandlerAttributeBuffer = $currentClass->getAttributes(QueryHandler::class);

        if (empty($queryHandlerAttributeBuffer)) {
            return false;
        }

        $queryHandlerAttribute = $queryHandlerAttributeBuffer[0];

        $queryHandlerAttributeInstance = $queryHandlerAttribute->newInstance();

        assert($queryHandlerAttributeInstance instanceof QueryHandler);

        $stack[$queryHandlerAttributeInstance->getTargetQueryClass()] = Container::getInstance()->make($currentClass->getName());

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

        $eventHandlerStack[$topic][] = Container::getInstance()->make($currentClass->getName());

        return true;
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    private static function pushToTopicStructMap(&$eventStruckStack, ReflectionClass $currentClass) : bool
    {
        $eventStructAttributeBuffer = $currentClass->getAttributes(EventStruct::class);

        if (empty($eventStructAttributeBuffer)) {
            return false;
        }

        $eventStructAttribute = $eventStructAttributeBuffer[0];

        $eventStructAttributeInstance = $eventStructAttribute->newInstance();

        assert($eventStructAttributeInstance instanceof EventStruct);

        $topic = $eventStructAttributeInstance->getTopic();

        if (isset($eventStruckStack[$topic])) {
            throw new Exception("Multiple structs for topic $topic");
        }

        $eventStruckStack[$topic] = $currentClass->getName();

        return true;
    }
}

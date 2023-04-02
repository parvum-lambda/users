<?php

namespace App\Console\Commands;

use Carbon\Exceptions\Exception;
use FilesystemIterator;
use Generator;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\ConsumedMessage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RegexIterator;
use Support\Attributes\Router\ClassParser\ClassParser;
use Support\CQRS\Attributes\EventConsumer;
use Support\CQRS\Interfaces\DataSet;

class KafkaConsumerCommand extends Command
{
    protected $signature = 'kafka:consume-all';

    protected $description = 'A Kafka Consumer for Laravel.';

    private array $config;

    private array $topicTypeMap = [];

    public function __construct()
    {
        parent::__construct();

        $this->config = [
            'brokers' => config('kafka.brokers'),
        ];
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function handle()
    {
        $this->getConsumers();
    }

    /**
     * @throws ReflectionException|Exception
     */
    private function getConsumers() : void
    {
        foreach ($this->getClasses() as $fullClass) {
            $class = new ReflectionClass($fullClass);

            if ($class->isAbstract()) {
                continue;
            }

            $eventConsumerAttributeBuffer = $class->getAttributes(EventConsumer::class);

            if (empty($eventConsumerAttributeBuffer)) {
                continue;
            }

            $eventConsumerAttribute = $eventConsumerAttributeBuffer[0];

            $eventConsumerAttributeInstance = $eventConsumerAttribute->newInstance();

            assert($eventConsumerAttributeInstance instanceof EventConsumer);

            $topic = $eventConsumerAttributeInstance->getTopic();

            if (! isset($this->topicTypeMap[$topic])) {
                $this->topicTypeMap[$topic] = [];
            }

            $this->topicTypeMap[$topic][] = app($fullClass);
        }

        foreach ($this->topicTypeMap as $topic => $_) {
            echo 'listening: ' . $topic . PHP_EOL;
            $consumer = Kafka::createConsumer([$topic])
            ->withBrokers($this->config['brokers'])
            ->withAutoCommit()
            ->withHandler(function (ConsumedMessage $message) {
                $topic = $message->getHeaders()['topic'];
                $body = $message->getBody();

                if (isset($this->topicTypeMap[$topic])) {
                    foreach ($this->topicTypeMap[$topic] as $consumer) {
                        assert($consumer instanceof \Support\CQRS\Interfaces\EventConsumer);

                        $consumer->handle(new class($body) implements DataSet {
                            public function __construct(private readonly array $data)
                            {
                            }

                            public function getData() : array
                            {
                                return $this->data;
                            }
                        });
                    }
                }
            })
            ->build();

            $consumer->consume();
        }
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

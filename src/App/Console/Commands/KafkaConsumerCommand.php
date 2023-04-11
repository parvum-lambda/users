<?php

namespace App\Console\Commands;

use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\ConsumedMessage;
use ReflectionException;
use Support\CQRS\CQRSService;
use Support\CQRS\Interfaces\DataSet;

class KafkaConsumerCommand extends Command
{
    protected $signature = 'kafka:consume-all';

    protected $description = 'A Kafka Consumer for Laravel.';

    private array $config;

    public function __construct(private readonly CQRSService $CQRSService)
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

    private function getConsumers() : void
    {
        $topics = $this->CQRSService->getTopics();

        foreach ($topics as $topic) {
            echo 'listening: ' . $topic . PHP_EOL;
            $consumer = Kafka::createConsumer([$topic])
            ->withBrokers($this->config['brokers'])
            ->withAutoCommit()
            ->withHandler(function (ConsumedMessage $message) {
                $topic = $message->getHeaders()['topic'];
                $body = $message->getBody();
                $consumers = $this->CQRSService->getHandlersForTopic($topic);

                foreach ($consumers as $consumer) {
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
            })
            ->build();

            $consumer->consume();
        }
    }
}

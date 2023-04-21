<?php

namespace App\Console\Commands;

use Carbon\Exceptions\Exception as CarbonException;
use Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\ConsumedMessage;
use Junges\Kafka\Message\Message;
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
     * @throws CarbonException
     */
    public function handle()
    {
        $this->getConsumers();
    }

    /**
     * @throws CarbonException
     * @throws Exception
     */
    private function getConsumers() : void
    {
        $topics = $this->CQRSService->getTopics();

        foreach ($topics as $topic) {
            echo 'listening: ' . $topic . PHP_EOL;

            try {
                $this->attachConsumer($topic);
            } catch (Exception $exception) {
                if ($exception->getMessage() === 'Broker: Unknown topic or partition') {
                    $this->touchTopic($topic);
                    $this->attachConsumer($topic);
                }

                throw $exception;
            }
        }
    }

    /**
     * @param string $topic
     * @return void
     * @throws CarbonException
     */
    private function attachConsumer(string $topic) : void
    {
        $consumer = Kafka::createConsumer([$topic])
            ->withBrokers($this->config['brokers'])
            ->withAutoCommit()
            ->withHandler(function (ConsumedMessage $message) {
                $topic = $message->getHeaders()['topic'];

                if (! isset($topic)) {
                    return;
                }

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

    /**
     * @throws Exception
     */
    private function touchTopic(string $topic)
    {
        echo 'touching: ' . $topic . PHP_EOL;
        $kafkaProducer = Kafka::publishOn($topic);

        $kafkaProducer->withMessage(
            new Message(
                headers: [],
                body: null,
            )
        );

        $kafkaProducer->send();
    }
}

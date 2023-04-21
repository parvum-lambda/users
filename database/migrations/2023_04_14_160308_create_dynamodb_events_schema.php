<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;

return new class extends Migration {
    private Aws\DynamoDb\DynamoDbClient $dynamoDbClient;
    private array $config = [];
    private const TABLE_NAME = 'events';

    public function __construct()
    {
        $this->config['endpoint'] = config('dynamodb.connections.' . config('app.env') . '.endpoint');
        $aws = App::make('aws');
        assert($aws instanceof Aws\Sdk);
        $this->dynamoDbClient = $aws->createClient('dynamodb', $this->config);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        $tableName = self::TABLE_NAME;
        $this->dynamoDbClient->createTable([
            'TableName' => $tableName,
            'KeySchema' => [
                [
                    'AttributeName' => 'entityId',
                    'KeyType'       => 'HASH',
                ]
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'entityId',
                    'AttributeType' => 'B'
                ],
            ],
            'ProvisionedThroughput' => ['ReadCapacityUnits' => 4, 'WriteCapacityUnits' => 2],
        ]);

        echo PHP_EOL . 'Waiting for table...' . PHP_EOL;
        $this->dynamoDbClient->waitUntil('TableExists', ['TableName' => $tableName]);
        echo "table $tableName created!";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        $this->dynamoDbClient->deleteTable([
            'TableName' => self::TABLE_NAME,
        ]);
    }
};

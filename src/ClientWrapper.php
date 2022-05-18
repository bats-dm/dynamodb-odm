<?php

declare(strict_types=1);

namespace Core\DynamodbOdm;

use Aws\DynamoDb\DynamoDbClient;

class ClientWrapper
{
    private DynamoDbClient $client;

    public function __construct(DynamoDbClient $client)
    {
        $this->client = $client;
    }

    public function batchPut(string $tableName, $items)
    {
        $result = $this->client->batchWriteItem([
            'RequestItems' => [
                'PutRequest' => [
//                    'Item' =>
                ]
            ]
        ]);
    }
}

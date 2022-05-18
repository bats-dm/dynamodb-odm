<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\DB;

use Aws\DynamoDb\DynamoDbClient;
use Core\DynamodbOdm\DocumentDBConverter;
use Core\DynamodbOdm\Mapping\DocumentMetadataFactory;

class DocumentPersister
{
    private DynamoDbClient $client;

    private DocumentDBConverter $documentDBConverter;

    private DocumentMetadataFactory $documentMetadataFactory;

    public function __construct(
        DynamoDbClient $client,
        DocumentDBConverter $documentDBConverter,
        DocumentMetadataFactory $documentMetadataFactory
    )
    {
        $this->client = $client;
        $this->documentDBConverter = $documentDBConverter;
        $this->documentMetadataFactory = $documentMetadataFactory;
    }

    public function batchPut(array $documents): void
    {
        $groups = $this->groupDocumentsByTable($documents);
        $itemsByTable = [];

        foreach ($groups as $tableName => $documents) {
            $items = [];

            foreach ($documents as $document) {
                $items[] = [
                    'PutRequest' => [
                        'Item' => $this->documentDBConverter->toArray($document)
                    ]
                ];
            }

            $itemsByTable[$tableName] = $items;
        }

        $this->client->batchWriteItem([
            'RequestItems' => $itemsByTable
        ]);
    }

    private function groupDocumentsByTable(array $documents): array
    {
        $result = [];

        foreach ($documents as $document) {
            $metadata = $this->documentMetadataFactory->getMetadataFor(get_class($document));
            $result[$metadata->table][] = $document;
        }

        return $result;
    }
}

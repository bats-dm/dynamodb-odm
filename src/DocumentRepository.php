<?php

declare(strict_types=1);

namespace Core\DynamodbOdm;

use Core\DynamodbOdm\DB\DocumentPersister;

abstract class DocumentRepository
{
    protected DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function find(string $id): ?object
    {
        $response = $this->documentManager->getClient()->getItem([
            'TableName' => $this->getTableName(),
            'Key' => [
                'id' => ['S' => $id],
            ]
        ]);

        if ($response['Item']) {
            return $this->documentManager->getDocumentDBConverter()->toDocument(
                $response['Item'],
                $this->getClassName()
            );
        }

        return null;
    }

    public function findAll(): array
    {
        // TODO replace scan operation
        $response = $this->documentManager->getClient()->scan([
            'TableName' => $this->getTableName(),
        ]);

        $documents = [];

        foreach ($response['Items'] as $item) {
            $documents[] = $this->documentManager->getDocumentDBConverter()->toDocument(
                $item,
                $this->getClassName()
            );
        }

        return $documents;
    }

    public function findBy(/*array $criteria, ?array $orderBy = null, $limit = null, $offset = null*/): array
    {
        $response = $this->documentManager->getClient()->query([
            'TableName' => $this->getTableName(),
            'ExpressionAttributeValues' => [
                ':v1' => [
                    'S' => '62739085893e1',
                ],
            ],
            'KeyConditionExpression' => 'id = :v1',
        ]);

        dd($response['Items']);
    }

    public function findOneBy(array $criteria): ?object
    {

    }

    private function getTableName(): string
    {
        $metadata = $this->documentManager->getMetadataFactory()->getMetadataFor(
            $this->getClassName()
        );

        return $metadata->table;
    }

    /**
     * Repository class name
     */
    abstract public function getClassName(): string;
}

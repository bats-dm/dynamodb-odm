<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\DB;

use Aws\DynamoDb\DynamoDbClient;
use Core\DynamodbOdm\Mapping\DocumentMetadata;
use Core\DynamodbOdm\Mapping\DocumentMetadataFactory;
use Core\DynamodbOdm\Types\Type;

class BatchDocumentPersister
{
    private DynamoDbClient $client;

    private DocumentMetadataFactory $documentMetadataFactory;

    private array $items = [];

    public function __construct(DynamoDbClient $client, DocumentMetadataFactory $documentMetadataFactory)
    {
        $this->client = $client;
        $this->documentMetadataFactory = $documentMetadataFactory;
    }

    public function add(object $document, )
    {

    }

    public function create(object $document)
    {
        $dbFields = [];

        foreach ($this->documentMetadata->getFields() as $field) {
            $type = Type::getType($field->type);
            $method = 'get' . ucfirst($field->propertyName);
            $value = $document->{$method}();
            $dbFields[$field->propertyName] = [$type->getDatabaseTypeShortcut() => $type->convertToDatabaseValue($value)];
        }

        $this->client->putItem([
            'TableName' => $this->documentMetadata->table,
            'Item' => $dbFields
        ]);
    }
}

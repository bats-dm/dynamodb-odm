<?php

declare(strict_types=1);

namespace Core\DynamodbOdm;

use Aws\DynamoDb\DynamoDbClient;
use Core\DynamodbOdm\DB\DocumentPersister;
use Core\DynamodbOdm\Mapping\DocumentMetadataFactory;
use Doctrine\Common\Annotations\Reader;

class DocumentManager
{
    private DynamoDbClient $client;

    private DocumentMetadataFactory $documentMetadataFactory;

    private DocumentDBConverter $documentDBConverter;

    private DocumentPersister $documentPersister;

    private DocumentCollectionHandler $documentCollectionHandler;

    public function __construct(DynamoDbClient $client, Reader $annotationReader)
    {
        $this->client = $client;
        $this->documentMetadataFactory = new DocumentMetadataFactory($annotationReader);
        $this->documentDBConverter = new DocumentDBConverter($this->documentMetadataFactory);
        $this->documentPersister = new DocumentPersister(
            $this->client,
            $this->documentDBConverter,
            $this->documentMetadataFactory
        );
        $this->documentCollectionHandler = new DocumentCollectionHandler($this->documentPersister);
    }

    public function persist(object $document): void
    {
        $this->documentCollectionHandler->persist($document);
    }

    public function flush(): void
    {
        $this->documentCollectionHandler->commit();
    }

    public function getClient(): DynamoDbClient
    {
        return $this->client;
    }

    public function getMetadataFactory(): DocumentMetadataFactory
    {
        return $this->documentMetadataFactory;
    }

    public function getDocumentDBConverter(): DocumentDBConverter
    {
        return $this->documentDBConverter;
    }
}

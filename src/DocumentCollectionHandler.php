<?php

declare(strict_types=1);

namespace Core\DynamodbOdm;

use Core\DynamodbOdm\DB\DocumentPersister;

class DocumentCollectionHandler
{
    private DocumentPersister $documentPersister;

    private array $documentUpdates = [];

    private array $documentDeletions = [];

    public function __construct(DocumentPersister $documentPersister)
    {
        $this->documentPersister = $documentPersister;
    }

    public function persist(object $document): void
    {
        if (!$document->getId()) {
            $document->setId(Uuid::generate());
        }

        $this->documentUpdates[$document->getId()] = $document;
    }

    public function remove(object $document): void
    {
        if (!$document->getId()) {
            throw new \Exception('Document id is not found');
        }

        $this->documentDeletions[$document->getId()] = $document;
    }

    public function commit(): void
    {
        $this->doUpdateAll();
        $this->doRemoveAll();
    }

    private function doUpdateAll(): void
    {
        if (count($this->documentUpdates) > 0) {
            $this->documentPersister->batchPut($this->documentUpdates);
            $this->documentUpdates = [];
        }
    }

    private function doRemoveAll(): void
    {
        if (count($this->documentDeletions) > 0) {
//            $this->documentPersister->batchPut($this->documentUpdates);
            $this->documentDeletions = [];
        }
    }
}

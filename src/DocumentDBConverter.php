<?php

declare(strict_types=1);

namespace Core\DynamodbOdm;

use Core\DynamodbOdm\Mapping\DocumentMetadataFactory;
use Core\DynamodbOdm\Types\Type;

class DocumentDBConverter
{
    protected DocumentMetadataFactory $documentMetadataFactory;

    public function __construct(DocumentMetadataFactory $documentMetadataFactory)
    {
        $this->documentMetadataFactory = $documentMetadataFactory;
    }

    public function toArray(object $document): array
    {
        $className = get_class($document);
        $documentMetadata = $this->documentMetadataFactory->getMetadataFor($className);
        $array = [];

        foreach ($documentMetadata->getFields() as $field) {
            $type = Type::getType($field->type);
            $method = 'get' . ucfirst($field->propertyName);
            $value = $document->{$method}();
            $array[$field->propertyName] = [$type->getDatabaseTypeShortcut() => $type->convertToDatabaseValue($value)];
        }

        return $array;
    }

    public function toDocument(array $array, string $className): object
    {
        $documentMetadata = $this->documentMetadataFactory->getMetadataFor($className);
        $document = new $className();

        foreach ($documentMetadata->getFields() as $field) {
            $type = Type::getType($field->type);
            $method = 'set' . ucfirst($field->propertyName);
            $value = $type->convertToDatabaseValue($array[$field->propertyName][$type->getDatabaseTypeShortcut()]);
            $document->{$method}($value);
        }

        return $document;
    }
}

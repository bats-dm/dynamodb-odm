<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\Mapping;

use Core\DynamodbOdm\DocumentManager;
use Core\DynamodbOdm\Mapping\Annotations\Document;
use Core\DynamodbOdm\Mapping\Annotations\Field;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;

class DocumentMetadataFactory
{
    /** @var DocumentMetadata[] */
    private array $loadedMetadata = [];

    private Reader $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function getMetadataFor(string $className): ?DocumentMetadata
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        $this->loadedMetadata[$className] = $this->createMetadataFor($className);

        return $this->loadedMetadata[$className];
    }

    protected function createMetadataFor(string $classname): ?DocumentMetadata
    {
        $documentMetadata = new DocumentMetadata();
        $reflectionClass = new ReflectionClass($classname);

        $document = $this->annotationReader->getClassAnnotation(
            $reflectionClass,
            Document::class
        );

        if (!$document) {
            return null;
        }

        $documentMetadata->table = $document->table;

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }
            $propertyName = $reflectionProperty->getName();
            $this->reflectionProperties[$propertyName] = $reflectionProperty;

            /** @var Field $field */
            $field = $this->annotationReader->getPropertyAnnotation($reflectionProperty, Field::class);
            if (!$field) {
                continue;
            }

            $fieldMetadata = new FieldMetadata();
            $fieldMetadata->propertyName = $propertyName;
            $fieldMetadata->fieldName = $field->name ? : $propertyName;
            $fieldMetadata->type = $field->type;

            $documentMetadata->addField($fieldMetadata);
        }

        return $documentMetadata;
    }
}

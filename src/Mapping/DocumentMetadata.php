<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\Mapping;

class DocumentMetadata
{
    public string $table;

    /** @var FieldMetadata[] */
    protected array $fields = [];

    /**
     * @return FieldMetadata[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(FieldMetadata $fieldMetadata)
    {
        $this->fields[] = $fieldMetadata;
    }

    public function getField(string $name): ?FieldMetadata
    {
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\Mapping\Annotations;

/**
 * Identifies a class as a document that can be stored in the database
 *
 * @Annotation
 * @Target("CLASS")
 */
class Document
{
    public string $table;
}

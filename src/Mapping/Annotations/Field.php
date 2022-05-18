<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\Mapping\Annotations;

/**
 * Specifies a generic field mapping
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Field
{
    public ?string $name = null;

    public string $type = 'string';

    public bool $nullable = false;
}

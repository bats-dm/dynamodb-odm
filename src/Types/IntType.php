<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\Types;

/**
 * The Int type.
 */
class IntType extends Type
{
    public function getDatabaseTypeShortcut(): string
    {
        return 'N';
    }

    public function convertToDatabaseValue($value)
    {
        return $value !== null ? (string) $value : null;
    }

    public function convertToPHPValue($value)
    {
        return $value !== null ? (int) $value : null;
    }
}

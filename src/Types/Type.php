<?php

declare(strict_types=1);

namespace Core\DynamodbOdm\Types;

use InvalidArgumentException;
use DateTimeInterface;

use function gettype;

abstract class Type
{
    public const INTEGER = 'integer';
    public const STRING = 'string';
    public const DATE = 'date';

    /** @var Type[] Map of already instantiated type objects. One instance per type (flyweight). */
    private static array $typeObjects = [];

    private static array $typesMap = [
        self::INTEGER => IntType::class,
        self::STRING => StringType::class,
        self::DATE => DateType::class,
    ];

    public function getDatabaseTypeShortcut(): string
    {
        return 'S';
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value)
    {
        return $value;
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value)
    {
        return $value;
    }

    /**
     * Get a Type instance.
     */
    public static function getType(string $type): Type
    {
        if (!isset(self::$typesMap[$type])) {
            throw new InvalidArgumentException(sprintf('Invalid type specified "%s".', $type));
        }

        if (!isset(self::$typeObjects[$type])) {
            $className = self::$typesMap[$type];
            self::$typeObjects[$type] = new $className();
        }

        return self::$typeObjects[$type];
    }

    /**
     * Get a Type instance based on the type of the passed php variable.
     *
     * @param mixed $variable
     *
     * @throws InvalidArgumentException
     */
    public static function getTypeFromPHPVariable($variable): ?Type
    {
        if (is_object($variable)) {
            if ($variable instanceof DateTimeInterface) {
                return self::getType('date');
            }
        } else {
            $type = gettype($variable);
            switch ($type) {
                case 'integer':
                    return self::getType('int');
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertPHPToDatabaseValue($value)
    {
        $type = self::getTypeFromPHPVariable($value);
        if ($type !== null) {
            return $type->convertToDatabaseValue($value);
        }

        return $value;
    }
}

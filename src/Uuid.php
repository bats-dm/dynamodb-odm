<?php

declare(strict_types=1);

namespace Core\DynamodbOdm;

class Uuid
{
    public static function generate(): string
    {
        return uniqid(); // TODO replace algorithm
    }
}

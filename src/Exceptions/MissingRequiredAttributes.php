<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Exceptions;

use InvalidArgumentException;

use function sprintf;

final class MissingRequiredAttributes extends InvalidArgumentException
{
    public static function fromArray(string $fields): self
    {
        return new self(
            sprintf(
                'Missing required fields, please check your cycle.php config. Missing fields "%s"',
                $fields
            ),
            400
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Exception;

use App\Interface\AppExceptionInterface;
use Exception;

class UnexpectedTypeException extends Exception implements AppExceptionInterface
{
    public function __construct(mixed $value, string $expectedType)
    {
        parent::__construct(sprintf('Expected argument of type "%s", "%s" given', $expectedType, get_debug_type($value)));
    }
}

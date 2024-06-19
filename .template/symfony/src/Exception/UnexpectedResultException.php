<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use App\Interface\AppExceptionInterface;

class UnexpectedResultException extends Exception implements AppExceptionInterface
{
}

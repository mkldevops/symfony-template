<?php

declare(strict_types=1);

namespace App\Exception;

use App\Interface\AppExceptionInterface;
use Exception;

class UnexpectedResultException extends Exception implements AppExceptionInterface
{
}

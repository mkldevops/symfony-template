<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use App\Interface\AppExceptionInterface;

#[WithHttpStatus(Response::HTTP_BAD_REQUEST)]
class EntityValidationException extends Exception implements AppExceptionInterface
{
}

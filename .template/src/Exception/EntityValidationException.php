<?php

namespace App\Exception;

use App\Interface\AppExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_BAD_REQUEST)]
class EntityValidationException extends Exception implements AppExceptionInterface
{
}

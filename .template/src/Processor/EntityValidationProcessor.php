<?php

namespace App\Processor;

use App\Exception\EntityValidationException;
use App\Interface\EntityInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class EntityValidationProcessor
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws EntityValidationException
     */
    public function preProcess(string $id, object $object): void
    {
        if (!$object instanceof EntityInterface) {
            return;
        }

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            $errors = array_map(
                static fn ($error): string => sprintf(
                    "- %s: %s\n",
                    $error->getPropertyPath(),
                    $error->getMessage()
                ),
                iterator_to_array($errors)
            );
            $errors = implode("\n", $errors);

            $message = sprintf(
                "Error when validating fixture %s (%s), violation(s) detected:\n%s",
                $id, $object::class, $errors
            );
            throw new EntityValidationException($message);
        }
    }
}

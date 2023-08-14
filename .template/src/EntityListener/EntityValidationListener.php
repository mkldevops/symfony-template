<?php

namespace App\EntityListener;

use App\Exception\EntityValidationException;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 1000)]
#[AsDoctrineListener(event: Events::preUpdate, priority: 1000)]
final readonly class EntityValidationListener
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws EntityValidationException
     */
    public function __invoke(PrePersistEventArgs|PreUpdateEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $constraints = $this->validator->validate($entity);

        if ($constraints->count() <= 0) {
            return;
        }

        $messages = array_map(
            static fn ($constraint) => $constraint->getMessage(),
            iterator_to_array($constraints)
        );

        throw new EntityValidationException('Validation failed : '.implode("\n", $messages));
    }
}

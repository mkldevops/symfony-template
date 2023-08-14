<?php

declare(strict_types=1);

namespace App\Trait;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait SoftDeleteableEntityTrait
{
    #[Groups(['deleted_at', 'deleted_at:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTime $deletedAt = null;

    public function setDeletedAt(DateTime $deletedAt = null): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}

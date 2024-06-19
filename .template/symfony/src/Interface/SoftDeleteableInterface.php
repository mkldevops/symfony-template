<?php

declare(strict_types=1);

namespace App\Interface;

use DateTime;

interface SoftDeleteableInterface extends EntityInterface
{
    public function setDeletedAt(DateTime $deletedAt = null): static;

    public function getDeletedAt(): ?DateTime;

    public function isDeleted(): bool;
}

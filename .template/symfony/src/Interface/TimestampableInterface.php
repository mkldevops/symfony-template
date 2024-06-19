<?php

declare(strict_types=1);

namespace App\Interface;

use DateTime;

interface TimestampableInterface extends EntityInterface
{
    public function setCreatedAt(DateTime $createdAt): static;

    public function getCreatedAt(): ?DateTime;

    public function setUpdatedAt(DateTime $updatedAt): static;

    public function getUpdatedAt(): ?DateTime;
}

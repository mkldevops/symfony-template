<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait IsEnabledEntityTrait
{
    #[Groups(['is_enabled', 'is_enabled:write'])]
    #[Assert\NotNull]
    #[ORM\Column(options: ['default' => true])]
    protected bool $isEnabled = true;

    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }
}

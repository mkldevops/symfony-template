<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait TeaserEntityTrait
{
    #[Groups(['teaser', 'teaser:write'])]
    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $teaser = null;

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function setTeaser(?string $teaser): static
    {
        $this->teaser = $teaser;

        return $this;
    }
}

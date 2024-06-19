<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait PriceEntityTrait
{
    #[Groups(['price', 'price:write'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[ORM\Column]
    protected float $price = 0.00;

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}

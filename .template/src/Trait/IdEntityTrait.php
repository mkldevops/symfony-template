<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

use function is_string;

trait IdEntityTrait
{
    #[Groups(['id', 'id:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid|string $id): static
    {
        $this->id = is_string($id) ? Uuid::fromString($id) : $id;

        return $this;
    }
}

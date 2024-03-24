<?php

namespace App\Entity;

use App\Repository\PlayerRoundRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PlayerRoundRepository::class)]
class PlayerRound
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column]
    private ?\DateTimeImmutable $creationDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastUpdateDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Round $round = null;

    #[ORM\Column(type: Types::JSON)]
    private array $currentCards = [];

    #[ORM\Column]
    private ?int $wager = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeImmutable $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLastUpdateDate(): ?\DateTimeImmutable
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate(\DateTimeImmutable $lastUpdateDate): static
    {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): static
    {
        $this->round = $round;

        return $this;
    }

    public function getCurrentCards(): array
    {
        return $this->currentCards;
    }

    public function setCurrentCards(array $currentCards): static
    {
        $this->currentCards = $currentCards;

        return $this;
    }

    public function getWager(): ?int
    {
        return $this->wager;
    }

    public function setWager(int $wager): static
    {
        $this->wager = $wager;

        return $this;
    }
}

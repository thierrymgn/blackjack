<?php

namespace App\Entity;

use App\Repository\PlayerRoundRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PlayerRoundRepository::class)]
class PlayerRound
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['game', 'round', 'playerRound'])]
    private ?Uuid $id;

    #[ORM\Column]
    #[Groups(['game', 'round', 'playerRound'])]
    private ?\DateTimeImmutable $creationDate = null;

    #[ORM\Column]
    #[Groups(['game', 'round', 'playerRound'])]
    private ?\DateTimeImmutable $lastUpdateDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn]
    #[Groups(['game', 'round', 'playerRound'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['playerRound', 'game'])]
    private array $currentCards = [];

    #[ORM\Column]
    #[Groups(['playerRound', 'game'])]
    private ?int $wager = null;

    #[ORM\ManyToOne(inversedBy: 'playerRounds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['playerRound'])]
    private ?Round $round;

    #[ORM\Column(length: 255)]
    #[Groups(['game', 'round', 'playerRound'])]
    private ?string $status = null;

    public function getId(): ?Uuid
    {
        return $this->id;
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
    
    public function getCurrentCards(): array
    {
        return $this->currentCards;
    }

    public function setCurrentCards(array $currentCards): static
    {
        $this->currentCards = $currentCards;

        return $this;
    }

    public function addToCurrentCards(array $cards): static
    {
        foreach ($cards as $card) {
            $this->currentCards[] = $card;
        }

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

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): static
    {
        $this->round = $round;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }    
}

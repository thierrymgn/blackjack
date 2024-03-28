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

        $this->checkStatusChange();

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

    public function checkStatusChange(): void
    {
        $score = $this->calculateScore();

        if ($score > 21) {
            $this->setStatus('busted');
        } elseif ($score === 777) {
            $this->setStatus('blackjack');
        }
    }

    public function calculateScore(): int
    {
        $score = 0;
        $figuresCount = 0;
        $acesCount = 0;
        $aceValue = 11;

        foreach($this->currentCards as $card) {
            $value = $card[1];
            if(is_numeric($value)) {
                $score += $value;
            } elseif ($value === 'A') {
                $acesCount++;
            } else {
                $score += 10; 
                $figuresCount++;
            }
        }

        for($i = 0; $i < $acesCount; $i++) {
            if ($score + $aceValue > 21) {
                $score += 1;
            } else {
                $score += $aceValue;
            }
        }

        // if there are 2 cards in hand, and one of them is an ace, and the other one is a figure, then it's a blackjack
        if (count($this->currentCards) === $figuresCount + $acesCount) {
            return 777;
        }

        return $score;
    }
}

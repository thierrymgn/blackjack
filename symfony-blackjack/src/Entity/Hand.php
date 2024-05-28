<?php

namespace App\Entity;

use App\Repository\HandRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HandRepository::class)]
class Hand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUpdateDate = null;

    #[ORM\Column]
    private array $cards = [];

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?bool $isBlackjack = null;

    #[ORM\Column]
    private ?bool $isBusted = null;

    #[ORM\OneToOne(mappedBy: 'playerHand', cascade: ['persist', 'remove'])]
    private ?Turn $turn = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLastUpdateDate(): ?\DateTimeInterface
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate(\DateTimeInterface $lastUpdateDate): static
    {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function setCards(array $cards): static
    {
        $this->cards = $cards;

        return $this;
    }

    public function addCard(Card $card): static
    {
        $this->cards[] = $card;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getIsBlackjack(): ?bool
    {
        return $this->isBlackjack;
    }

    public function setIsBlackjack(bool $isBlackjack): static
    {
        $this->isBlackjack = $isBlackjack;

        return $this;
    }

    public function getIsBusted(): ?bool
    {
        return $this->isBusted;
    }

    public function setIsBusted(bool $isBusted): static
    {
        $this->isBusted = $isBusted;

        return $this;
    }

    public function getTurn(): ?Turn
    {
        return $this->turn;
    }

    public function setTurn(Turn $turn): static
    {
        // set the owning side of the relation if necessary
        if ($turn->getPlayerHand() !== $this) {
            $turn->setPlayerHand($this);
        }

        $this->turn = $turn;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\TurnRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TurnRepository::class)]
class Turn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'turns')]
    private ?Game $game = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::JSON)]
    private array $deck = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUpdateDate = null;

    #[ORM\Column(type: Types::JSON)]
    private array $playerHand = [];

    #[ORM\Column(type: Types::JSON)]
    private array $dealerHand = [];

    #[ORM\Column(nullable: true)]
    private ?int $wager = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

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

    /**
     * @return Card[]
     */
    public function getDeck(): array
    {
        return $this->deck;
    }

    /**
     * @param Card[] $deck
     */
    public function setDeck(array $deck): static
    {
        $this->deck = $deck;

        return $this;
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


    /**
     * @return Card[]
     */
    public function getPlayerHand(): array
    {
        return $this->playerHand;
    }

    /**
     * @param Card[] $deck
     */
    public function setPlayerHand(array $playerHand): static
    {
        $this->playerHand = $playerHand;

        return $this;
    }

    public function addCardToPlayerHand(Card $card): static
    {
        $this->playerHand[] = $card;

        return $this;
    }

    /**
     * @return Card[]
     */
    public function getDealerHand(): array
    {
        return $this->dealerHand;
    }

    /**
     * @param Card[] $deck
     */
    public function setDealerHand(array $dealerHand): static
    {
        $this->dealerHand = $dealerHand;

        return $this;
    }

    public function addCardToDealerHand(Card $card): static
    {
        $this->dealerHand[] = $card;

        return $this;
    }

    public function getWager(): ?int
    {
        return $this->wager;
    }

    public function setWager(?int $wager): static
    {
        $this->wager = $wager;

        return $this;
    }
}

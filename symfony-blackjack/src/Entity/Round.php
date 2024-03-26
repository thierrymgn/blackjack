<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RoundRepository::class)]
class Round
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

    #[ORM\Column(type: Types::JSON)]
    private array $cardsLeft = [];

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'rounds')]
    #[ORM\JoinColumn]
    private ?Game $game;

    #[ORM\Column(type: Types::JSON)]
    private array $dealerCards = [];

    #[ORM\OneToMany(targetEntity: PlayerRound::class, mappedBy: 'round', orphanRemoval: true)]
    private Collection $playerRounds;

    public function __construct()
    {
        $this->playerRounds = new ArrayCollection();
    }

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

    #[Ignore]
    public function getCardsLeft(): array
    {
        return $this->cardsLeft;
    }

    public function setCardsLeft(array $cardsLeft): static
    {
        $this->cardsLeft = $cardsLeft;

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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getDealerCards(): array
    {
        return $this->dealerCards;
    }

    public function setDealerCards(array $dealerCards): static
    {
        $this->dealerCards = $dealerCards;

        return $this;
    }

    /**
     * @return Collection<int, PlayerRound>
     */
    public function getPlayerRounds(): Collection
    {
        return $this->playerRounds;
    }

    public function addPlayerRound(PlayerRound $playerRound): static
    {
        if (!$this->playerRounds->contains($playerRound)) {
            $this->playerRounds->add($playerRound);
            $playerRound->setRound($this);
        }

        return $this;
    }

    public function removePlayerRound(PlayerRound $playerRound): static
    {
        if ($this->playerRounds->removeElement($playerRound)) {
            // set the owning side to null (unless already changed)
            if ($playerRound->getRound() === $this) {
                $playerRound->setRound(null);
            }
        }

        return $this;
    }
}

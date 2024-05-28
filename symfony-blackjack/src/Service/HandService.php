<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\Game;
use App\Entity\Hand;
use App\Entity\Turn;
use App\Entity\User;
use App\Form\Turn\WageTurnFormType;
use App\Repository\HandRepository;
use App\Repository\TurnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class HandService
{

    private HandRepository $handRepository;
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $em;

    public function __construct(HandRepository $handRepository, FormFactoryInterface $formFactory, EntityManagerInterface $em)
    {
        $this->handRepository = $handRepository;
        $this->formFactory = $formFactory;
        $this->em = $em;
    }

    public function calculateScore(Hand $hand): array
    {
        $score = 0;
        $aces = 0;
        $faces = 0;
        foreach ($hand->getCards() as $card) {
            $value = $card->getValue();
            if ($value === 'J' || $value === 'Q' || $value === 'K') {
                $score += 10;
                $faces++;
            } elseif ($value === 'A') {
                $aces++;
            } else {
                $score += (int) $value;
            }
        }

        for ($i = 0; $i < $aces; $i++) {
            if ($score + 11 <= 21) {
                $score += 11;
            } else {
                $score += 1;
            }
        }

        $hand->setIsBusted($score > 21);
        $hand->setScore($score);

        if(count($hand->getCards()) === $faces + $aces && count($hand->getCards()) === 2 && $score === 21){
            $hand->setIsBlackjack(true);
        }

        $this->handRepository->save($hand, true);

        return [$hand, null];
    }

}
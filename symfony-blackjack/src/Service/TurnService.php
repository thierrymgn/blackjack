<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\Game;
use App\Entity\Turn;
use App\Entity\User;
use App\Form\Turn\WageTurnFormType;
use App\Repository\TurnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TurnService
{

    private TurnRepository $turnRepository;
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $em;
    private HandService $handService;

    public function __construct(TurnRepository $turnRepository, FormFactoryInterface $formFactory, EntityManagerInterface $em, HandService $handService)
    {
        $this->turnRepository = $turnRepository;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->handService = $handService;
    }

    public function createNewTurn(Game $game): array
    {
        list($_, $err) = $this->checkIsAbleToCreateNewTurn($game);
        if($err instanceof \Error) {
            return [null, $err];
        }

        $turn = $this->generateTurn($game);
        $deck = self::shuffleDeck(self::generateDeck());
        $turn->setDeck($deck);
        $turn->setStatus('waging');

        $this->turnRepository->save($turn, true);

        return [$turn, null];
    }

    public function checkIsAbleToCreateNewTurn(Game $game): array
    {
        if($game->getStatus() !== 'playing') {
            return [null, new \Error('The game has not started', 409)];
        }

        if($game->getTurns()->count() === 0) {
            return [$game, null];
        }

        $lastTurn = $game->getTurns()->last();
        if(!in_array($lastTurn->getStatus(), ['draw', 'won', 'lost'])) {
            return [null, new \Error('A turn is already playing', 409)];
        }

        return [$game, null];
    }

    public function generateTurn(Game $game): Turn
    {
        $turn = new Turn();
        $turn->setGame($game);
        $turn->setCreationDate(new \DateTime());
        $turn->setLastUpdateDate(new \DateTime());

        return $turn;
    }

    public static function generateDeck(): array
    {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

        $deck = [];
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = new Card($suit, $value);
            }
        }

        return $deck;
    }

    public static function shuffleDeck(array $deck): array
    {
        shuffle($deck);

        return $deck;
    }

    public function getTurn(string $id, User $user): array
    {
        $turn = $this->turnRepository->findOneBy(['id' => $id]);
        if($turn === null) {
            return [null, new \Error('Turn not found', 404)];
        }

        if($turn->getGame()->getUser()->getId() !== $user->getId()) {
            return [null, new \Error('You are not allowed to see this turn', 403)];
        }

        return [$turn, null];
    }

    public function wageTurn(string $id, User $user, array $data): array
    {
        list($turn, $err) = $this->getTurn($id, $user);
        if($err instanceof \Error) {
            return [null, $err];
        }

        if($turn->getStatus() !== 'waging') {
            return [null, new \Error('You can not wage this turn', 409)];
        }

        list($turn, $errors) = $this->checkPayloadIsValidForWageTurn($data, $turn);
        if(!empty($errors)) {
            $err = new \Error(json_encode($errors), 400);
            return [null, $err];
        }

        $user->setWallet($user->getWallet() - $turn->getWage());
        $user->setLastUpdateDate(new \DateTime());
        $this->em->getRepository(User::class)->save($user);

        $turn->setLastUpdateDate(new \DateTime());
        $turn->setStatus('initializing');
        $this->em->getRepository(Turn::class)->save($user);

        return [$turn, null];
    }

    public function checkPayloadIsValidForWageTurn(array $data, Turn $turn): array
    {
        $form = $this->formFactory->create(WageTurnFormType::class, $turn);
        $form->submit($data);
        $errors = [];

        if(!$form->isValid()) {
            $errors = FormService::getFormErrors($form);
            return [null, $errors];
        }

        return [$turn, null];
    }

    public function initializeTurn(Turn $turn): array
    {
        list($turn, $err) = $this->playerDrawTopCard($turn);
        list($turn, $err) = $this->playerDrawTopCard($turn);
        
        list($turn, $err) = $this->dealerDrawTopCard($turn);

        $turn->setStatus('playing');
        $turn->setLastUpdateDate(new \DateTime());
        $this->turnRepository->save($turn);

        return [$turn, null];
    }

    public function playerDrawTopCard(Turn $turn): array
    {
        if(!in_array($turn->getStatus(), ['playing', 'initializing'])) {
            return [null, new \Error('You can not draw a card', 409)];
        }

        if(count($turn->getDeck()) === 0) {
            $deck = self::shuffleDeck(self::generateDeck());
            $turn->setDeck($deck);
        }

        $deck = $turn->getDeck();
        $card = array_shift($deck);
        $turn->setDeck($deck);
        $turn->getPlayerHand()->addCard($card);

        $this->handService->calculateScore($turn->getPlayerHand());

        $this->turnRepository->save($turn);

        return [$turn, null];
    }

    public function dealerDrawTopCard(Turn $turn): array
    {
        if(!in_array($turn->getStatus(), ['dealer', 'initializing'])) {
            return [null, new \Error('You can not draw a card', 409)];
        }

        if(count($turn->getDeck()) === 0) {
            $deck = self::shuffleDeck(self::generateDeck());
            $turn->setDeck($deck);
        }

        $deck = $turn->getDeck();
        $card = array_shift($deck);
        $turn->setDeck($deck);
        $turn->getDealerHand()->addCard($card);

        $this->handService->calculateScore($turn->getDealerHand());

        $this->turnRepository->save($turn);

        return [$turn, null];
    }

    public function hitTurn(string $id, User $user): array
    {
        list($turn, $err) = $this->getTurn($id, $user);
        if($err instanceof \Error) {
            return [null, $err];
        }

        list($turn, $err) = $this->playerDrawTopCard($turn);
        if($err instanceof \Error) {
            return [null, $err];
        }

        if($turn->getPlayerHand()->getIsBusted()) {
            $turn->setStatus('busted');
            $this->turnRepository->save($turn);
        }

        return [$turn, null];
    }

    public function standTurn(string $id, User $user): array
    {
        list($turn, $err) = $this->getTurn($id, $user);
        if($err instanceof \Error) {
            return [null, $err];
        }

        $turn->setStatus('dealer');
        $this->turnRepository->save($turn);

        return [$turn, null];
    }

    public function dealerAutoDraw(Turn $turn): array
    {
        if($turn->getStatus() !== 'dealer') {
            return [null, new \Error('You can not draw a card', 409)];
        }

        while($turn->getDealerHand()->getScore() < 17) {
            list($turn, $err) = $this->dealerDrawTopCard($turn);
            if($err instanceof \Error) {
                return [null, $err];
            }
        }

        $turn->setStatus('distributeGains');

        return [$turn, null];
    }

    public function distributeGains(Turn $turn): array
    {
        if($turn->getStatus() !== 'distributeGains') {
            return [null, new \Error('You can not distribute gains', 409)];
        }

        $turn->setLastUpdateDate(new \DateTime());

        if($turn->getPlayerHand()->getIsBusted()) {
            $turn->setStatus('lost');
            $this->em->getRepository(Turn::class)->save($turn);
            return [$turn, null];
        }

        $gains = $turn->getWager();

        if($turn->getPlayerHand()->getIsBlackjack()) {
            $gains = $gains * 2;
        }

        if($turn->getDealerHand()->getIsBusted() === true) {
 
            $turn->getGame()->getUser()->addToWallet($gains + $turn->getWager());
            $turn->setStatus('won');
            $this->em->getRepository(Turn::class)->save($turn);
            $this->em->getRepository(User::class)->save($turn->getGame()->getUser());

            return [$turn, null];
        }

        if($turn->getDealerHand()->getScore() > $turn->getPlayerHand()->getScore()) {
            $turn->setStatus('lost');
            $this->em->getRepository(Turn::class)->save($turn);
            return [$turn, null];
        }

        if($turn->getDealerHand()->getIsBlackjack() && $turn->getPlayerHand()->getIsBlackjack()) {
            $turn->setStatus('draw');
            $turn->getGame()->getUser()->addToWallet($turn->getWager());
            $this->em->getRepository(Turn::class)->save($turn);
            $this->em->getRepository(User::class)->save($turn->getGame()->getUser());
            return [$turn, null];
        }

        if($turn->getDealerHand()->getScore() === $turn->getPlayerHand()->getScore()) {
            $turn->getGame()->getUser()->addToWallet($turn->getWager());
            $turn->setStatus('draw');
            $this->em->getRepository(Turn::class)->save($turn);
            $this->em->getRepository(User::class)->save($turn->getGame()->getUser());
            return [$turn, null];
        }


        $turn->getGame()->getUser()->addToWallet($gains + $turn->getWager());
        $turn->setStatus('won');
        $this->em->getRepository(Turn::class)->save($turn);
        $this->em->getRepository(User::class)->save($turn->getGame()->getUser());
            
        return [$turn, null];
    }

}
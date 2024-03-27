<?php

namespace App\Service\User;

use App\DTO\Response\Error;
use App\DTO\Response\Success;
use App\DTO\User\CreateUserDTO;
use App\Entity\User;
use App\Form\User\CreateUserType;
use App\Form\User\UpdateUserType;
use App\Service\Form\FormService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private PasswordHasherFactoryInterface $passwordHasherFactory;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, PasswordHasherFactoryInterface $passwordHasherFactory, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->passwordHasherFactory = $passwordHasherFactory;
        $this->logger = $logger;
    }

    public function getPaginatedUserList(int $limit = 12, int $page = 0): array
    {
        $users = $this->entityManager->getRepository(User::class)->findBy([], [], $limit, $page * $limit);
        $totalUsers = $this->entityManager->getRepository(User::class)->countUsers();

        $response = [
            'limit' => $limit,
            'page' => $page+1,
            'content' => $users,
            'total' => $totalUsers  
        ];

        return [$response, null];
    }

    public function getUserByUuid(string $uuid): array
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $uuid]);

        if(empty($user)) {
            return [null, new Error(['error' => 'User not found'], 404)];
        }

        return [$user, null];
    }

    public function createUser(array $payload): array
    {
        list($createUserDTO, $err) = $this->createUserValidatePayload($payload);

        if($err !== null) {
            $this->logger->error('Invalid user creation', ['errors' => $err->getContent(), 'payload' => $payload]);
            return [null, $err];
        }

        $user = $this->createUserFromDTO($createUserDTO);

        $this->entityManager->getRepository(User::class)->save($user, true);

        $this->logger->info('User created', ['user' => $user]);

        return [$user, null];
    }

    private function createUserValidatePayload(array $payload, ): array
    {
        $createUserDTO = new CreateUserDTO();
        $form = $this->formFactory->create(CreateUserType::class, $createUserDTO);

        $form->submit($payload);

        $errors = [];

        if(!$form->isValid()) {
            $errors = FormService::getFormErrors($form);
        }

        $usernameAlreadyExists = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $createUserDTO->getUsername()]);
        $emailAlreadyExists = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $createUserDTO->getEmail()]);

        if(empty($usernameAlreadyExists) === false) {
            $form->get('username')->addError(new FormError('Username already exists'));
        }

        if(empty($emailAlreadyExists) === false) {
            $form->get('email')->addError(new FormError('Email already exists'));
        }

        $errors = array_merge($errors, FormService::getFormErrors($form));

        if(!empty($errors)) {
            return [null, new Error($errors, 400)];
        }

        return [$createUserDTO, null];
    }

    public function updateUser(User $user, array $payload): array
    {
        list($updatedUser, $err) = $this->updateUserValidatePayload($payload, $user);

        if($err !== null) {
            $this->logger->error('Invalid user update', ['errors' => $err->getContent(), 'payload' => $payload]);
            return [null, $err];
        }

        $user->setLastUpdateDate(new \DateTime());

        $this->entityManager->getRepository(User::class)->save($updatedUser, false);
        $this->logger->info('User created', ['user' => $updatedUser]);

        return [$updatedUser, null];
    }

    private function updateUserValidatePayload(array $payload, User $user): array
    {
        $form = $this->formFactory->create(UpdateUserType::class, $user);

        $form->submit($payload, false);

        $errors = [];

        if(!$form->isValid()) {
            $errors = FormService::getFormErrors($form);
        }

        $usernameAlreadyExists = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
        $emailAlreadyExists = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        if(empty($usernameAlreadyExists) === false) {
            $form->get('username')->addError(new FormError('Username already exists'));
        }

        if(empty($emailAlreadyExists) === false) {
            $form->get('email')->addError(new FormError('Email already exists'));
        }

        $errors = array_merge($errors, FormService::getFormErrors($form));

        if(!empty($errors)) {
            return [null, new Error($errors, 400)];
        }

        return [$user, null];
    }

    private function createUserFromDTO(CreateUserDTO $createUserDTO): User
    {
        $user = new User();
        $user->setEmail($createUserDTO->getEmail());
        $user->setUsername($createUserDTO->getUsername());

        $hashedPassword = $this->passwordHasherFactory->getPasswordHasher(User::class)->hash($createUserDTO->getPassword());

        $user->setPassword($hashedPassword);
        $user->setCreationDate(new \DateTime());
        $user->setLastUpdateDate(new \DateTime());
        $user->setWallet(1000);

        return $user;
    }

    public function deleteUser(User $user): array
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->logger->info('User deleted', ['user' => $user]);

        return [null, null];
    }

}

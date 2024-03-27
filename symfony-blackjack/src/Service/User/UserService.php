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

        return $response;
    }

    public function getUserByUuid(string $uuid): Success | Error
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $uuid]);

        if(empty($user)) {
            return new Error(['error' => 'User not found'], 404);
        }

        return new Success(['user' => $user], 200);
    }

    public function createUser(array $payload): Success | Error
    {
        $createUserDTO = $this->createUserValidatePayload($payload);

        if($createUserDTO instanceof Error) {
            $this->logger->error('Invalid user creation', ['errors' => $createUserDTO->getContent(), 'payload' => $payload]);
            return $createUserDTO;
        }

        $user = $this->createUserFromDTO($createUserDTO);

        $this->entityManager->getRepository(User::class)->save($user, true);

        $this->logger->info('User created', ['user' => $user]);

        return new Success(['user' => $user], 201);
    }

    private function createUserValidatePayload(array $payload, ): CreateUserDTO | Error
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
            return new Error($errors, 400);
        }

        return $createUserDTO;
    }

    public function updateUser(User $user, array $payload): Success | Error
    {
        $updatedUser = $this->updateUserValidatePayload($payload, $user);

        if($updatedUser instanceof Error) {
            $this->logger->error('Invalid user update', ['errors' => $updatedUser->getContent(), 'payload' => $payload]);
            return $updatedUser;
        }

        $user->setLastUpdateDate(new \DateTime());

        $this->entityManager->getRepository(User::class)->save($user, false);
        $this->logger->info('User created', ['user' => $user]);

        return new Success(['user' => $user], 200);
    }

    private function updateUserValidatePayload(array $payload, User $user): User | Error
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
            return new Error($errors, 400);
        }

        return $user;
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

    public function deleteUser(User $user): Success
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->logger->info('User deleted', ['user' => $user]);

        return new Success([], 204);
    }

}

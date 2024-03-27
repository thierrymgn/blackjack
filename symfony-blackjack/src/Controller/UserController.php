<?php

namespace App\Controller;

use App\DTO\Response\Error;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/user', name: 'get_user_list', methods: ['GET'])]
    public function getUserList(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $limit = $request->query->get('limit', 12);
        $page = $request->query->get('page', 0);

        list($users, $err) = $this->userService->getPaginatedUserList($limit, $page);

        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($users, 200, [], ['groups' => 'user']);
    }

    #[Route('/user', name: 'post_user', methods: ['POST'])]
    public function postUser(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if(null === $payload) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        list($user, $err) = $this->userService->createUser($payload);

        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($user, 201, [], ['groups' => 'user']);
    }

    #[Route('/user/profile', name: 'get_current_user_infos', methods: ['GET'])]
    public function getCurrentUserInfos(Request $request): JsonResponse
    {
        return $this->json($this->getUser(), 200, [], ['groups' => 'user']);
    }

    #[Route('/user/{uuid}', name: 'get_user_infos', methods: ['GET'])]
    public function getUserInfos(string $uuid): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        list($user, $err) = $this->userService->getUserByUuid($uuid);

        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($user, 200, [], ['groups' => 'user']);
    }

    #[Route('/user/profile', name: 'patch_current_user_infos', methods: ['PATCH'])]
    public function patchCurrentUserInfos(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $user = $this->getUser();

        list($user, $err) = $this->userService->updateUser($user, $payload);

        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($user, 200, [], ['groups' => 'user']);
    }

    #[Route('/user/{uuid}', name: 'patch_user_infos', methods: ['PATCH'])]
    public function patchUserInfos(Request $request, string $uuid): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        list($user, $err) = $this->userService->getUserByUuid($uuid);
        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        $payload = json_decode($request->getContent(), true);

        list($user, $err) = $this->userService->updateUser($user, $payload);
        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json($user, 200, [], ['groups' => 'user']);
    }

    #[Route('/user/profile', name: 'delete_current_user_infos', methods: ['DELETE'])]
    public function deleteCurrentUserInfos(): JsonResponse
    {
        $user = $this->getUser();
        list($user, $err) = $this->userService->deleteUser($user);

        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }

        return $this->json([], 204);
    }

    #[Route('/user/{uuid}', name: 'delete_user_infos', methods: ['DELETE'])]
    public function deleteUserInfos(Request $request, string $uuid): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        list($user, $err) = $this->userService->getUserByUuid($uuid);
        if($err instanceof Error) {
            return $this->json($err->getContent(), $err->getCode());
        }
        
        list($user, $err) = $this->userService->deleteUser($user);
        return $this->json([], 204);
    }
}

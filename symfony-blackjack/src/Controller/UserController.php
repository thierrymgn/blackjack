<?php

namespace App\Controller;

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
    public function getUserList(): JsonResponse
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userService->getUserList();
        return $this->json($users);
    }

    #[Route('/user', name: 'post_user', methods: ['POST'])]
    public function postUser(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        $response = $this->userService->createUser($payload);
        return $this->json($response->getContent(), $response->getCode());
    }

    #[Route('/user/profile', name: 'get_current_user_infos', methods: ['GET'])]
    public function getCurrentUserInfos(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/{uuid}', name: 'get_user_infos', methods: ['GET'])]
    public function getUserInfos(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/profile', name: 'patch_current_user_infos', methods: ['PATCH'])]
    public function patchCurrentUserInfos(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/{uuid}', name: 'patch_user_infos', methods: ['PATCH'])]
    public function patchUserInfos(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/profile', name: 'delete_current_user_infos', methods: ['DELETE'])]
    public function deleteCurrentUserInfos(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/{uuid}', name: 'delete_user_infos', methods: ['DELETE'])]
    public function deleteUserInfos(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}

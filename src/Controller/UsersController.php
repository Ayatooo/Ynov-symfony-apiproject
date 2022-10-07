<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsersController.php',
        ]);
    }

    #[Route('/api/users', name: 'users.getAll', methods: ['GET'])]
    public function getRestaurant(UsersRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $users = $repository->findAll();
        $data = $serializer->serialize($users, 'json', ['groups' => 'showUsers']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

}

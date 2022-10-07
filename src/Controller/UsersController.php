<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    #[Route('/api/users/{idUsers}', name: 'users.getOne', methods: ['GET'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function getOneRestaurant(Users $users, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($users, 'json', ['groups' => 'showUsers']), Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.delete', methods: ['DELETE'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function deleteRestaurant(Users $users, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($users);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT, [], false);
    }
}

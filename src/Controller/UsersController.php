<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
    public function getUsers(UsersRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $users = $repository->findAll();
        $data = $serializer->serialize($users, 'json', ['groups' => 'showUsers']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.getOne', methods: ['GET'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function getOneUsers(Users $users, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($users, 'json', ['groups' => 'showUsers']), Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.delete', methods: ['DELETE'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function deleteUsers(Users $users, EntityManagerInterface $entityManager): JsonResponse
    {
        $users->setStatus(false);
        $entityManager->flush();
        return new JsonResponse('User deleted', Response::HTTP_OK);
    }

    #[Route('/api/users', name: 'users.create', methods: ['POST'])]
    public function createUser(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $users = $serializer->deserialize($data, Users::class, 'json');
        $users->setStatus(true);
        $errors = $validator->validate($users);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($users);
        $entityManager->flush();
        $jsonUser = $serializer->serialize($users, 'json', ['groups' => 'showUsers']);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }
}

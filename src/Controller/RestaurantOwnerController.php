<?php

namespace App\Controller;

use App\Entity\RestaurantOwner;
use App\Repository\RestaurantOwnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class RestaurantOwnerController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RestaurantOwnerController.php',
        ]);
    }

    #[Route('/api/users', name: 'users.getAll', methods: ['GET'])]
    public function getUsers(Request $request, RestaurantOwnerRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $limit = $limit > 20 ? 20 : $limit;

        $users = $repository->findWithPagination($page, $limit);
        $data = $serializer->serialize($users, 'json', ['groups' => 'showUsers']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.getOne', methods: ['GET'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function getOneUsers(RestaurantOwner $users, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($users, 'json', ['groups' => 'showUsers']), Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.delete', methods: ['DELETE'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function deleteUsers(RestaurantOwner $users, EntityManagerInterface $entityManager): JsonResponse
    {
        $users->setStatus(false);
        $entityManager->flush();
        return new JsonResponse('User deleted', Response::HTTP_OK);
    }

    #[Route('/api/users', name: 'users.create', methods: ['POST'])]
    public function createUser(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $users = $serializer->deserialize($data, RestaurantOwner::class, 'json');
        $users->setStatus("true");
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

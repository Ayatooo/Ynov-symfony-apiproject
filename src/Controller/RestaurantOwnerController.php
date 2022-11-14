<?php

namespace App\Controller;

use App\Entity\RestaurantOwner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\RestaurantOwnerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public function getUsers(Request $request, RestaurantOwnerRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $limit = $limit > 20 ? 20 : $limit;
        $idCache = 'getRestaurantOwners';
        $data = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer, $page, $limit) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantOwnerCache');
            $users = $repository->findWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['showRestaurantOwners']);
            return $serializer->serialize($users, 'json', $context);
        });

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.getOne', methods: ['GET'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    public function getOneUsers(RestaurantOwner $users, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $idCache = 'getRestaurantOwner' . $users->getId();
        $data = $cache->get($idCache, function (ItemInterface $item) use ($users, $serializer) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantOwnerCache');
            $context = SerializationContext::create()->setGroups(['showRestaurantOwners']);
            return $serializer->serialize($users, 'json', $context);
        });
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{idUsers}', name: 'users.delete', methods: ['DELETE'])]
    #[ParamConverter('users', options: ['id' => 'idUsers'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function deleteUsers(RestaurantOwner $users, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['restaurantOwnerCache']);
        $users->setStatus(false);
        $entityManager->flush();
        return new JsonResponse('User deleted', Response::HTTP_OK);
    }

    #[Route('/api/users', name: 'users.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function createUser(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['restaurantOwnerCache']);
        $data = $request->getContent();
        $users = $serializer->deserialize($data, RestaurantOwner::class, 'json');
        $users->setStatus("true");
        $errors = $validator->validate($users);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($users);
        $entityManager->flush();
        $context = SerializationContext::create()->setGroups(['showRestaurantOwners']);
        $jsonUser = $serializer->serialize($users, 'json', $context);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }
}

<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantOwnerRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class RestaurantController extends AbstractController
{
    #[Route('/restaurant', name: 'app_restaurant')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RestaurantController.php',
        ]);
    }

    #[Route('/api/restaurant', name: 'restaurant.getAll', methods: ['GET'])]
    public function getRestaurant(RestaurantRepository $repository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $limit = $limit > 20 ? 20 : $limit;

        $idCache = 'getRestaurant';
        $data = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer, $page, $limit) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantCache');
            $restaurant = $repository->findWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['showRestaurant']);
            return $serializer->serialize($restaurant, 'json', $context);
        });
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurant/{idRestaurant}', name: 'restaurant.getOne', methods: ['GET'])]
    #[ParamConverter('restaurant', options: ['id' => 'idRestaurant'])]
    public function getOneRestaurant(Restaurant $restaurant, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $idCache = 'getOneRestaurant' . $restaurant->getId();
        $data = $cache->get($idCache, function (ItemInterface $item) use ($restaurant, $serializer) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantCache');
            $context = SerializationContext::create()->setGroups(['showRestaurant']);
            return $serializer->serialize($restaurant, 'json', $context);
        });
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurant/{idRestaurant}', name: 'restaurant.put', methods: ['PUT'])]
    #[ParamConverter('restaurant', options: ['id' => 'idRestaurant'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function updateRestaurant(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, Restaurant $restaurant, TagAwareCacheInterface $cache): JsonResponse
    {
        $data = $serializer->deserialize(
            $request->getContent(),
            Restaurant::class,
            'json'
        );

        $restaurant->setRestaurantName($data->getRestaurantName() ? $data->getRestaurantName() : $restaurant->getRestaurantName());
        $restaurant->setRestaurantPhone($data->getRestaurantPhone() ? $data->getRestaurantPhone() : $restaurant->getRestaurantPhone());
        $restaurant->setRestaurantDescription($data->getRestaurantDescription() ? $data->getRestaurantDescription() : $restaurant->getRestaurantDescription());
        $restaurant->setRestaurantLatitude($data->getRestaurantLatitude() ? $data->getRestaurantLatitude() : $restaurant->getRestaurantLatitude());
        $restaurant->setRestaurantLongitude($data->getRestaurantLongitude() ? $data->getRestaurantLongitude() : $restaurant->getRestaurantLongitude());

        $cache->invalidateTags(['restaurantCache']);
        $context = SerializationContext::create()->setGroups(['showRestaurant']);
        $response = $serializer->serialize($restaurant, 'json', $context);
        return new JsonResponse($response, Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurant/{idRestaurant}', name: 'restaurant.delete', methods: ['DELETE'])]
    #[ParamConverter('restaurant', options: ['id' => 'idRestaurant'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function deleteRestaurant(Restaurant $restaurant, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['restaurantCache']);
        $restaurant->setStatus("false");
        $entityManager->flush();
        return new JsonResponse('Restaurant supprimé', Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurant', name: 'restaurant.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function createRestaurant(ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request, restaurantOwnerRepository $usersRepository, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['restaurantCache']);
        $restaurant = $serializer->deserialize($request->getContent(), Restaurant::class, 'json');
        $restaurant->setStatus("true");

        $content = $request->toArray();
        $idOwner = $content['idOwner'];
        $owner = $usersRepository->find($idOwner);

        $errors = $validator->validate($restaurant);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $restaurant->setRestaurantOwner($owner);

        $entityManager->persist($restaurant);
        $entityManager->flush();
        $context = SerializationContext::create()->setGroups(['showRestaurant']);
        $jsonRestaurant = $serializer->serialize($restaurant, 'json', $context);
        return new JsonResponse($jsonRestaurant, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/closest/restaurant/', name: 'restaurant.closest', methods: ['GET'])]
    public function getClosestRestaurant(SerializerInterface $serializer, Request $request, RestaurantRepository $restaurantRepository, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $limit = $limit > 20 ? 20 : $limit;

        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $distance = $request->query->get('distance');

        $cacheId = 'getClosestRestaurant' . $latitude . $longitude . $distance;
        $data = $cache->get($cacheId, function (ItemInterface $item) use ($restaurantRepository, $serializer, $page, $limit, $latitude, $longitude, $distance) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantCache');
            $restaurant = $restaurantRepository->findClosestRestaurant($latitude, $longitude, $distance, $page, $limit);
            $context = SerializationContext::create()->setGroups(['showRestaurant']);
            return $serializer->serialize($restaurant, 'json', $context);
        });
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}

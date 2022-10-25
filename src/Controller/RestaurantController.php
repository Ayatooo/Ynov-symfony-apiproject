<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\UsersRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/api/restaurants', name: 'restaurants.getAll', methods: ['GET'])]
    public function getRestaurant(RestaurantRepository $repository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $limit = $limit > 20 ? 20 : $limit;

        $restaurants = $repository->findWithPagination($page, $limit);
        $data = $serializer->serialize($restaurants, 'json', ['groups' => 'showRestaurants']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurants/{idRestaurant}', name: 'restaurants.getOne', methods: ['GET'])]
    #[ParamConverter('restaurant', options: ['id' => 'idRestaurant'])]
    public function getOneRestaurant(Restaurant $restaurant, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($restaurant, 'json', ['groups' => 'showRestaurants']), Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurants/{idRestaurant}', name: 'restaurants.delete', methods: ['DELETE'])]
    #[ParamConverter('restaurant', options: ['id' => 'idRestaurant'])]
    public function deleteRestaurant(Restaurant $restaurant, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $restaurant->setStatus(false);
        $entityManager->flush();
        return new JsonResponse($serializer->serialize("Delete done !", 'json', ['groups' => 'showRestaurants']), Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurants', name: 'restaurants.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour accéder à cette ressource')]
    public function createRestaurant(ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request, UsersRepository $usersRepository): JsonResponse
    {
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
        $jsonRestaurant = $serializer->serialize($restaurant, 'json', ['groups' => 'showRestaurants']);
        return new JsonResponse($jsonRestaurant, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/closest/restaurants/', name: 'restaurants.closest', methods: ['GET'])]
    public function getClosestRestaurant(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request, RestaurantRepository $restaurantRepository): JsonResponse
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $distance = $request->query->get('distance');

        $restaurants = $restaurantRepository->findClosestRestaurant($latitude, $longitude, $distance);
        dd($restaurants);

        $jsonRestaurant = $serializer->serialize($restaurants, 'json', ['groups' => 'showRestaurants']);
        return new JsonResponse($jsonRestaurant, Response::HTTP_OK, [], true);
    }
}

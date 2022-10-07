<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    public function getRestaurant(RestaurantRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $restaurants = $repository->findAll();
        $data = $serializer->serialize($restaurants, 'json');
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/restaurants/{idRestaurant}', name: 'restaurants.getOne', methods: ['GET'])]
    #[ParamConverter('restaurant', options: ['id' => 'idRestaurant'])]
    public function getOneRestaurant(Restaurant $restaurant, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($restaurant, 'json'), Response::HTTP_OK, [], true);
    }

    // #[Route('/api/restaurants/{id}', name: 'restaurants.getOne', methods: ['GET'])]
    // public function getOneRestaurant(RestaurantRepository $repository, SerializerInterface $serializer, int $id): JsonResponse
    // {
    //     $restaurant = $repository->find($id);
    //     $data = $serializer->serialize($restaurant, 'json');
    //     return new JsonResponse($data, Response::HTTP_OK, [], true);
    // }

}

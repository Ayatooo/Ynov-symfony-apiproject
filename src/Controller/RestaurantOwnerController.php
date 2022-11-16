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
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;



class RestaurantOwnerController extends AbstractController
{
    #[Route('/owner', name: 'app_owner')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RestaurantOwnerController.php',
        ]);
    }

    /**
     * Get a list of restaurants owners.
     */
    #[OA\Tag(name: 'Restaurants Owners')]
    #[Security(name: 'Bearer')]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'The page number',
        required: false,
        example: 1,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'The number of items per page',
        required: false,
        example: 10,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(new Model(type: RestaurantOwner::class))
        )
    )]
    #[Route('/api/owner', name: 'owner.getAll', methods: ['GET'])]
    public function getOwner(Request $request, RestaurantOwnerRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $limit = $limit > 20 ? 20 : $limit;
        $idCache = 'getRestaurantOwner';
        $data = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer, $page, $limit) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantOwnerCache');
            $owner = $repository->findWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['showRestaurantOwner']);
            return $serializer->serialize($owner, 'json', $context);
        });

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Get one restaurant owner.
     */
    #[OA\Tag(name: 'Restaurants Owners')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: RestaurantOwner::class)
    )]
    #[Route('/api/owner/{idOwner}', name: 'owner.getOne', methods: ['GET'])]
    #[ParamConverter('owner', options: ['id' => 'idOwner'])]
    public function getOneOwner(RestaurantOwner $owner, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $idCache = 'getRestaurantOwner' . $owner->getId();
        $data = $cache->get($idCache, function (ItemInterface $item) use ($owner, $serializer) {
            echo 'Cache saved 🧙‍♂️';
            $item->tag('restaurantOwnerCache');
            $context = SerializationContext::create()->setGroups(['showRestaurantOwner']);
            return $serializer->serialize($owner, 'json', $context);
        });
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Soft delete for a restaurant owner.
     */
    #[OA\Tag(name: 'Restaurants Owners')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Owner deleted',
    )]
    #[Route('/api/owner/{idOwner}', name: 'owner.delete', methods: ['DELETE'])]
    #[ParamConverter('owner', options: ['id' => 'idOwner'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function deleteowner(RestaurantOwner $owner, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['restaurantOwnerCache']);
        $owner->setStatus("false");
        $manager->flush();
        return new JsonResponse('Owner deleted', Response::HTTP_OK);
    }

    /**
     * Create a restaurant owner.
     */
    #[OA\RequestBody(
        request: 'RestaurantOwnerData',
        description: 'You have to fill all the fields',
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            ref: '#/components/schemas/RestaurantOwnerData'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: RestaurantOwner::class)
    )]
    #[OA\Tag(name: 'Restaurants Owners')]
    #[Security(name: 'Bearer')]
    #[Route('/api/owner', name: 'owner.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function createOwner(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['restaurantOwnerCache']);
        $data = $request->getContent();
        $owner = $serializer->deserialize($data, RestaurantOwner::class, 'json');
        $owner->setStatus("true");
        $errors = $validator->validate($owner);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($owner);
        $manager->flush();
        $context = SerializationContext::create()->setGroups(['showRestaurantOwner']);
        $jsonOwner = $serializer->serialize($owner, 'json', $context);

        return new JsonResponse($jsonOwner, Response::HTTP_CREATED, [], true);
    }

    /**
     * Update a restaurant owner.
     */
    #[OA\Tag(name: 'Restaurants Owners')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(new Model(type: RestaurantOwner::class))
        )
    )]
    #[OA\RequestBody(
        request: 'RestaurantOwnerData',
        description: 'You doesn\'t have to fill all the fields',
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            ref: '#/components/schemas/RestaurantOwnerData'
        )
    )]
    #[Route('/api/owner/{idOwner}', name: 'owner.update', methods: ['PUT'])]
    #[ParamConverter('owner', options: ['id' => 'idOwner'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour effectuer cette action')]
    public function updateOwner(RestaurantOwner $owner, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, TagAwareCacheInterface $cache): JsonResponse
    {
        $data = $serializer->deserialize(
            $request->getContent(),
            RestaurantOwner::class,
            'json',
        );

        $owner->setRestaurantOwnerFirstName($data->getRestaurantOwnerFirstName() ? $data->getRestaurantOwnerFirstName() : $owner->getRestaurantOwnerFirstName());
        $owner->setRestaurantOwnerLastName($data->getRestaurantOwnerLastName() ? $data->getRestaurantOwnerLastName() : $owner->getRestaurantOwnerLastName());
        $owner->setRestaurantOwnerEmail($data->getRestaurantOwnerEmail() ? $data->getRestaurantOwnerEmail() : $owner->getRestaurantOwnerEmail());
        $owner->setRestaurantOwnerPassword($data->getRestaurantOwnerPassword() ? $data->getRestaurantOwnerPassword() : $owner->getRestaurantOwnerPassword());
        $cache->invalidateTags(['restaurantOwnerCache']);
        $context = SerializationContext::create()->setGroups(['showRestaurantOwner']);
        $response = $serializer->serialize($owner, 'json', $context);
        $manager->persist($owner);
        $manager->flush();
        return new JsonResponse($response, Response::HTTP_OK, [], true);
    }
}

<?php

namespace App\Controller;

use App\Entity\Picture;
use OpenApi\Attributes as OA;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PictureController extends AbstractController
{

    #[Route('/picture', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }

    /**
     * Get details of a picture.
     */
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Picture::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Picture not found',
    )]
    #[OA\Tag(name: 'Pictures')]
    #[Security(name: 'Bearer')]
    #[Route('/api/picture/{idPicture}', name: 'picture.getOne', methods: ['GET'])]
    #[ParamConverter('picture', options: ['id' => 'idPicture'])]
    public function getOnePicture(int $idPicture, SerializerInterface $serializer, Request $request, PictureRepository $pictureRepository, TagAwareCacheInterface $cache): JsonResponse
    {
        $picture = $pictureRepository->find($idPicture);
        $idCache = 'getOnePicture' . $picture->getId();
        $relativePath = $picture->getPublicPath() . '/' . $picture->getRealPath();
        $location = $request->getUriForPath('/');
        $location = str_replace('/assets', "assets", $relativePath);

        if ($picture !== null) {
            $data = $cache->get($idCache, function (ItemInterface $item) use ($picture, $serializer) {
                echo 'Cache saved 🧙‍♂️';
                $item->tag('pictureCache');
                $context = array(SerializationContext::create()->setGroups(['showPicture']));
                return $serializer->serialize($picture, 'json', $context);
            });
            return new JsonResponse($data, Response::HTTP_OK, ["Location" => $location], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    /**
     * Create a picture.
     */
    #[OA\RequestBody(
        request: 'PictureData',
        description: 'You have to fill all the fields',
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            ref: '#/components/schemas/PictureData'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Picture::class)
    )]
    #[OA\Tag(name: 'Pictures')]
    #[Security(name: 'Bearer')]
    #[Route('/api/picture', name: 'picture.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour accéder à cette ressource.')]
    public function createPicture(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $picture = new Picture();
        $file = $request->files->get('file');
        $picture->setFile($file);
        $picture->setMimeType($file->getMimeType());
        $picture->setRealName($file->getClientOriginalName());
        $picture->setPublicPath("/assets/pictures/");
        $picture->setStatus("true");

        $cache->invalidateTags(['pictureCache']);

        $entityManager->persist($picture);
        $entityManager->flush();

        $urlGenerator->generate('picture.getOne', ['idPicture' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonPicture = $serializer->serialize($picture, 'json', ['groups' => 'showPicture']);
        return new JsonResponse($jsonPicture, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/picture/{idPicture}', name: 'picture.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour accéder à cette ressource.')]
    public function deletePicture(int $idPicture, EntityManagerInterface $entityManager, PictureRepository $pictureRepository, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['pictureCache']);
        $picture = $pictureRepository->find($idPicture);
        $picture->setStatus("false");
        $entityManager->flush();
        return new JsonResponse('Picture deleted 🗡️', Response::HTTP_OK, [], true);
    }
}

<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PictureController extends AbstractController
{
    #[Route('/api/pictures/{idPicture}', name: 'picture.getOne', methods: ['GET'])]
    public function getPicture(int $idPicture, SerializerInterface $serializer, Request $request, PictureRepository $pictureRepository, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $picture = $pictureRepository->find($idPicture);
        $relativePath = $picture->getPublicPath() . '/' . $picture->getRealPath();
        $location = $request->getUriForPath('/');
        $location = str_replace('/assets', "assets", $relativePath);


        if($picture !== null) {
            return new JsonResponse($serializer->serialize($picture, 'json', ['groups' => 'showPictures']), Response::HTTP_OK, ["Location" => $location], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route('/picture', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }

    #[Route('/api/pictures', name: 'picture.create', methods: ['POST'])]
    public function createPicture(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $picture = new Picture();
        $file = $request->files->get('file');
        $picture->setFile($file);
        $picture->setMimeType($file->getMimeType());
        $picture->setRealName($file->getClientOriginalName());
        $picture->setPublicPath("/assets/pictures/");
        $picture->setStatus("true");

        $entityManager->persist($picture);
        $entityManager->flush();

        $urlGenerator->generate('picture.getOne', ['idPicture' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonPicture = $serializer->serialize($picture, 'json', ['groups' => 'showPictures']);
        return new JsonResponse($jsonPicture, Response::HTTP_CREATED, [], true);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatesController extends AbstractController
{
    #[Route('/rates', name: 'app_rates')]
    public function index(): Response
    {
        return $this->render('rates/index.html.twig', [
            'controller_name' => 'RatesController',
        ]);
    }
}

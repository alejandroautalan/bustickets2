<?php

declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;


class SiteController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'arg' => 1,
        ]);
    }
}

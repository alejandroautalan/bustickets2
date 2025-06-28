<?php

namespace App\Controller;

use App\Entity\Reserva;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MisboletosController extends AbstractController
{
    #[Route('/misboletos', name: 'app_misboletos')]
    public function index(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $user = $this->getUser();
        $reservas = $entityManager->getRepository(Reserva::class)->findBy(['user' => $user->getId()]);
        return $this->render('misboletos/reservas.html.twig', [
            'reservas' => $reservas
        ]);
    }
}

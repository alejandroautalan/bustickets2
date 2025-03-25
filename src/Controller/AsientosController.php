<?php

namespace App\Controller;

use App\Entity\Asientos;
use App\Form\AsientosType;
use App\Repository\AsientosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/asientos')]
final class AsientosController extends AbstractController
{
    #[Route(name: 'app_asientos_index', methods: ['GET'])]
    public function index(AsientosRepository $asientosRepository): Response
    {
        return $this->render('asientos/index.html.twig', [
            'asientos' => $asientosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_asientos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $asiento = new Asientos();
        $form = $this->createForm(AsientosType::class, $asiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($asiento);
            $entityManager->flush();

            return $this->redirectToRoute('app_asientos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('asientos/new.html.twig', [
            'asiento' => $asiento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_asientos_show', methods: ['GET'])]
    public function show(Asientos $asiento): Response
    {
        return $this->render('asientos/show.html.twig', [
            'asiento' => $asiento,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_asientos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Asientos $asiento, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AsientosType::class, $asiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_asientos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('asientos/edit.html.twig', [
            'asiento' => $asiento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_asientos_delete', methods: ['POST'])]
    public function delete(Request $request, Asientos $asiento, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$asiento->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($asiento);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_asientos_index', [], Response::HTTP_SEE_OTHER);
    }
}

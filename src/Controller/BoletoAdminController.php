<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController;
use Doctrine\ORM\EntityManagerInterface;


final class BoletoAdminController extends CRUDController
{
    public function asignarasientoAction(
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $boleto = $this->admin->getSubject();
        $boleto->setEstado(2);

        $entityManager->persist($boleto);
        $entityManager->flush();

        return $this->redirectToRoute('admin_app_servicio_boleto_list', ['id' => $boleto->getServicio()->getId()]);
    }

}

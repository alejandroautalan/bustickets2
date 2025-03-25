<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Routing\Attribute\Route;

final class ViajeAdminController extends CRUDController
{
    public function asientosAction()
    {
        $asientos = $this->admin->getSubject()->getPasajes();

        return $this->render('ViajeAdmin/asientos.html.twig', [
            'controller_name' => 'ViajeAdminController',
            'asientos' => $asientos,
        ]);
    }
    public function ocuparAsientoAction()
    {
        $asientos = $this->admin->getSubject()->getPasajes();

        return $this->render('ViajeAdmin/ocuparAsiento.html.twig', [
            'controller_name' => 'ViajeAdminController',
            'asientos' => $asientos,
            'viajeid'    => $this->admin->getSubject()->getId()
        ]);
    }
}

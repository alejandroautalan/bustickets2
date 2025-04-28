<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Reserva;
// use App\Repository\TrayectoRepository;
// use App\Model\Reserva;
// use App\Form\Type\ReservaType;


final class ServicioAdminController extends CRUDController
{
    public function reservaAction(
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $servicio = $this->admin->getSubject();
        $trayecto = $servicio->getTrayecto();
        $reserva = new Reserva();
        $reserva->setServicio($servicio);
        $reserva->setOrigen($trayecto->getOrigen());
        $reserva->setDestino($trayecto->getDestino());

        $entityManager->persist($reserva);
        $entityManager->flush();

        return $this->redirectToRoute('admin_app_reserva_edit', ['id' => $reserva->getId()]);
    }

    /*
    public function reservaOldAction(TrayectoRepository $trayecto_repo)
    {
        $servicio = $this->admin->getSubject();
        $trayecto = $servicio->getTrayecto();
        $origen = $trayecto_repo->getOrigen($trayecto);
        $destino = $trayecto_repo->getDestino($trayecto);
        $reserva = new Reserva();
        $reserva->setOrigen($origen->getParada());
        $reserva->setDestino($destino->getParada());
        #print_r($reserva->getDestino()->getId()); die;
        $field_description = $this->admin->createFieldDescription('pasajero', [
            'translation_domain' => $this->admin->getTranslationDomain(),
            #'edit' => 'list',
        ]);
        $field_description->setAssociationAdmin($this->admin);
        $form = $this->createForm(
            ReservaType::class,
            $reserva,
            ['pasajero_model_manager' => $this->admin->getModelManager(),
             'pasajero_field_description' => $field_description]);

        return $this->render('ServicioAdmin/reservar.html.twig', [
            'controller_name' => 'ServicioAdminController',
            'object' => $servicio,
            'objectId' => $servicio->getId(),
            'action' => 'reserva',
            'servicio' => $servicio,
            'form' => $form,
        ]);
    }
    */

}

<?php

declare(strict_types=1);

namespace App\Admin;

use MercadoPago\Client\Preference\PreferenceClient;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;

use App\Admin\BaseAdmin;
use App\Form\Type\AsientoSelectorType;

use App\Entity\Reserva;
use App\Entity\Servicio;
use App\Entity\Boleto;
use App\Entity\Pago;
use App\Entity\TransporteAsiento;
use MercadoPago\MercadoPagoConfig;


final class ReservaAdmin extends BaseAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('estado')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('estado')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $reserva = $this->getSubject();
        $servicio = $reserva->getServicio();

        if(null == $servicio) {
            $form
                #->add('id')
                ->add('estado')
                ->add('origen', null, ['disabled' => true,])
                ->add('destino', null, ['disabled' => true,])
            ;
        } else {
            $this->configureReservaForm($form);
        }
    }

    protected function configureReservaForm(FormMapper $form): void
    {
        $reserva = $this->getSubject();
        $servicio = $reserva->getServicio();

        $reserva_repo = $this->getEntityRepository(Reserva::class);
        $asientos_libres = $reserva_repo->get_asientos_libres($servicio->getId());
        $asientos_reserva = $reserva_repo->get_asientos_reserva($reserva->getId());

        $estado = $reserva->getEstado();
        $form
        #->add('id')
        #->add('estado')
        ->ifTrue($estado == Reserva::STATE_DRAFT)
        ->with('Reserva - Selección Asientos')
        ->add('origen', null, ['disabled' => true,])
        ->add('destino', null, ['disabled' => true,])
        ->add('servicio', null, ['disabled' => true,])
        ->add('asientos', AsientoSelectorType::class, [
            'label' => 'Asientos disponibles',
            'transporte' => $servicio->getTransporte(),
            'asientos_libres' => $asientos_libres,
            'asientos_reserva' => $asientos_reserva,
            'required' => false,
            'mapped' => false])
       ->add('boletos', CollectionType::class, [
           'btn_add' => false,
           'type_options' => [
                'label' => false,
               'btn_add' => false,
               // Prevents the "Delete" option from being displayed
               'delete' => false,]
            ], [
                'btn_add' => false,
             #'edit' => 'inline',
             #'inline' => 'table',
             'label' => false,
            ])
       ->end()
       ->ifEnd()
       ->ifTrue($estado == Reserva::STATE_PENDING_PAYMENT)
       ->with('Reserva - Pago')
       ->add('pagos', CollectionType::class, [
           'label' => 'Pago',
           'btn_add' => false,
           'type_options' => [
               'label' => false,
               'btn_add' => false,
               'delete' => false,],
        ])
       ->ifEnd()

        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('estado')
        ;
    }

    protected function postUpdate(object $object): void
    {
        $request = $this->getRequest();
        $finalize = $request->get('btn_finalize', null);
        if(null !== $finalize) {
            $this->finalizeReserva($object);
        }

        $reserva = $object;
        $payment = $request->get('btn_payment', null);
        if(null !== $payment) {
            ##mercado pago###
            $pago = $reserva->getPagos()->last();
            MercadoPagoConfig::setAccessToken("APP_USR-7745628252612000-050318-f7578701336f67a894934818b76bc06f-2418800269");
            $client = new PreferenceClient();
            $preference = $client->create([
                "items"=> array(
                    array(
                        "title" => "El Santigueño Bus",
                        "quantity" => 1,
                        "unit_price" => ($pago->getImporteRecibido()/100)
                    )
                )
            ]);
            $preference->back_urls = array(
                "success" => "https://localhost:8000/admin/app/pago/success?id=".$pago->getId(),
                "failure" => "https://localhost:8000/admin/app/pago/failure?id=".$pago->getId(),
                "pending" => "https://localhost:8000/admin/app/pago/pending?id=".$pago->getId(),
            );
            $preference->auto_return = "https://localhost:8000/admin/app/pago/success?id=".$pago->getId();
            echo var_dump($preference);exit;
            $entityManager = $this->getEntityManager(Reserva::class);
            $reserva->setUrlpago($preference->init_point);#init_point
            $reserva->setEstado(Reserva::STATE_PENDING_PAYMENT);
            $entityManager->persist($reserva);
            $entityManager->flush();
        }

        $payment = $request->get('btn_boletos', null);
        if(null !== $payment) {
            $entityManager = $this->getEntityManager(Reserva::class);
            $reserva->setEstado(Reserva::STATE_DRAFT);
            $entityManager->persist($reserva);
            $entityManager->flush();
        }
    }

    protected function finalizeReserva($object)
    {
        $entityManager = $this->getEntityManager(Reserva::class);
        $object->setEstado(Reserva::STATE_COMPLETED);
        $entityManager->persist($object);
        foreach($object->getBoletos() as $b) {
            $b->setEstado(Boleto::STATE_RESERVED);
            $entityManager->persist($b);
        }
        $entityManager->flush();
    }

    protected function preUpdate(object $reserva): void
    {
        $request = $this->getRequest();
        $toggle_asiento = $request->get('toggle_asiento', null);
        if(null !== $toggle_asiento) {
            $this->addOrRemoveAsiento($reserva, $toggle_asiento);
            $this->addOrRemovePago($reserva);
        }

        $payment_clicked = $request->get('btn_payment', null);
        if(null !== $payment_clicked) {
            $reserva->recalcularPago();
        }
    }

    protected function addOrRemovePago(Reserva $reserva): void
    {
        // Esto se ejecuta ante de guardar el formulario
        $has_boletos = $reserva->getBoletos()->count() > 0;
        $has_pagos = $reserva->getPagos()->count() > 0;
        if($has_boletos && !$has_pagos) {
            $entityManager = $this->getEntityManager(Pago::class);
            $pago = new Pago();
            $pago->setTipo(Pago::PAYMENT_TYPE_UNSPECIFIED);
            $total = $reserva->calcularMontoTotal();
            $porcentaje = $total*0.1;
            $pago->setMonto($total);
            $pago->setImporteRecibido((int)$porcentaje);
            $reserva->addPago($pago);
            $entityManager->persist($pago);

        }
        if($has_pagos && !$has_boletos) {
            $entityManager = $this->getEntityManager(Pago::class);
            foreach($reserva->getPagos() as $pago) {
                $reserva->removePago($pago);
                $entityManager->remove($pago);
            }
        }
    }

    protected function addOrRemoveAsiento($object, $asiento_id)
    {
        // Esto se ejecuta ante de guardar el formulario
        $asientoRepo = $this->getEntityRepository(TransporteAsiento::class);

        $reserva = $object;
        $servicio = $reserva->getServicio();
        #$transporte = $servicio->getTransporte();
        $trayecto = $servicio->getTrayecto();
        $asiento = $asientoRepo->find($asiento_id);

        $asiento_boleto = null;
        $asiento_existe = false;
        foreach($reserva->getBoletos() as $boleto) {
            $basiento_id = $boleto->getAsiento()->getId();
            $aid = $asiento->getId();
            if($basiento_id == $aid) {
                $asiento_boleto = $boleto;
                $asiento_existe = true;
                break;
            }
        }
        $entityManager = $this->getEntityManager(Boleto::class);
        if(true == $asiento_existe) {
            # remover boleto
            $reserva->removeBoleto($boleto);
            $entityManager->remove($boleto);
        } else {
            # agregar boleto
            $boleto = new Boleto();
            $boleto->setServicio($servicio)
            ->setAsiento($asiento)
            ->setOrigen($trayecto->getOrigen())
            ->setDestino($trayecto->getDestino())
            ->setReserva($reserva)
            ->setEstado(Boleto::STATE_DRAFT)
            ;
            $entityManager->persist($boleto);
            $reserva->addBoleto($boleto);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Admin;

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
use App\Entity\TransporteAsiento;


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
                    'delete' => [],
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

        $form
        #->add('id')
        #->add('estado')
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
    }

    protected function finalizeReserva($object)
    {
        $entityManager = $this->getEntityManager(Reserva::class);
        $object->setEstado(Reserva::STATE_PAID_FINISHED);
        $entityManager->persist($object);
        foreach($object->getBoletos() as $b) {
            $b->setEstado(Boleto::STATE_RESERVED);
            $entityManager->persist($b);
        }
        $entityManager->flush();
    }

    protected function preUpdate(object $object): void
    {
        $request = $this->getRequest();
        $toggle_asiento = $request->get('toggle_asiento', null);
        if(null !== $toggle_asiento) {
            $this->addOrRemoveAsiento($object, $toggle_asiento);
        }
    }

    protected function addOrRemoveAsiento($object, $asiento_id)
    {
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

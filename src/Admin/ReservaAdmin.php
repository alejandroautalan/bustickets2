<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Resources\Preference;
use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;

use App\Admin\BaseAdmin;
use App\Form\Type\AsientoSelectorType;

use App\Entity\Pasajero;
use App\Entity\Reserva;
use App\Entity\Servicio;
use App\Entity\Boleto;
use App\Entity\Pago;
use App\Entity\TransporteAsiento;
use MercadoPago\MercadoPagoConfig;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


final class ReservaAdmin extends BaseAdmin
{
    protected function isFinalUser(): bool
    {
        $is_superadmin = $this->isGranted('ROLE_SUPER_RADMIN');
        $is_finaluser = $this->isGranted('ROLE_FINAL_USER');
        return (!$is_superadmin and $is_finaluser);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('estado')
        ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('addBoleto', 'addBoleto');
        $collection->add('modalForm', 'modalForm');

    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            #->add('id')
            ->add('estado', null, ['template' => 'ReservaAdmin/estado.html.twig'])
            ->add('origen')
            ->add('servicio.partida', null, ['label' => 'Dia y Hora'])
            ->add('destino')
            ->add('servicio.llegada', null, ['label' => 'Dia y Hora'])
            ->add('boletos', null, ['Boletos'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    #'show' => [],
                    #'edit' => [],
                    #'delete' => []
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
        ->with('Selección Asientos')
        ->add('origen', ModelHiddenType::class )
        ->add('destino', ModelHiddenType::class)
        ->add('servicio', ModelHiddenType::class)
        ->add('asientos', AsientoSelectorType::class, [
            'label' => 'Asientos disponibles',
            'transporte' => $servicio->getTransporte(),
            'asientos_libres' => $asientos_libres,
            'asientos_reserva' => $asientos_reserva,
            'idreserva' => $reserva->getId(),
            'required' => false,
            'mapped' => false])
       ->add('boletos', CollectionType::class, [
           'btn_add' => false,
           'disabled' => true,
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
       #->ifTrue($estado == Reserva::STATE_PENDING_PAYMENT)
       #->with('Pago')
       #    ->add('pagos', CollectionType::class, [
       #        'label' => false,
       #        'btn_add' => false,
       #        'type_options' => [
       #            'label' => false,
       #            'btn_add' => false,
       #            'delete' => false,],
       #     ])
       #->ifEnd()
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('estado')
        ;
    }

    public function prePersist(object $object): void
    {
        if (!$object instanceof Reserva) {
            return;
        }
        $user = $this->getUser();
        if ($user instanceof User) {
            $object->setUser($user);
        }
    }

    protected function postUpdate(object $object): void
    {
        $user = $this->getUser();
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
            $accessToken = $_ENV['ENV_ACCESS_TOKEN'];
            MercadoPagoConfig::setAccessToken($accessToken);
            $client = new PreferenceClient();
            $itemsForPreference = [
                [
                    "id" => (string)$reserva->getId(), // Asegúrate de que el ID sea string
                    "title" => "Santigueño Bus",
                    "quantity" => 1,
                    "unit_price" => (float)($pago->getImporteRecibido() / 100) // Asegúrate de que sea float
                ]
            ];

            $requestData = [
                "items" => $itemsForPreference,
                "back_urls" => [
                    "success" => $_ENV['ENV_BACK_URL'],
                    "failure" => $_ENV['ENV_BACK_URL'],
                    "pending" => $_ENV['ENV_BACK_URL'],
                ],
                "external_reference" => 'reserva_'.$reserva->getId().'_usuario_'.$user->getId(),
                "auto_return" => "all", // "all" o "approved"
                // Otros parámetros que necesites: payer, payment_methods, etc.
            ];
            $preference = $client->create($requestData);
            $entityManager = $this->getEntityManager(Reserva::class);
            $reserva->setUrlpago($preference->init_point);#init_point
            $reserva->setPreferenceId($preference->id);
            $reserva->setUser($user);
            #var_dump($preference);#$reserva->setPaymentId('123456');
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
        // Esto se ejecuta antes de guardar el formulario
        $user = $this->getUser();
        $has_boletos = $reserva->getBoletos()->count() > 0;
        $has_pagos = $reserva->getPagos()->count() > 0;
        if($has_boletos && !$has_pagos) {
            $entityManager = $this->getEntityManager(Pago::class);
            $pago = new Pago();
            $total = $reserva->calcularMontoTotal();
            $porcentaje = $total*0.1;
            $pago->setMonto($total);
            $pago->setUser($user);
            $pago->setTipo(Pago::PAYMENT_TYPE_MERCADOPAGO);
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
        $pasajero = null;
        if($this->isFinalUser()) {
            $userRepo = $this->getEntityRepository(User::class);
            $pasajero = $userRepo->getPasajeroForUser(
                $this->getUser(),
                $this->getEntityRepository(Pasajero::class)
            );
        }

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
            ->setPasajero($pasajero)
            ->setEstado(Boleto::STATE_DRAFT)
            ;
            $entityManager->persist($boleto);
            $reserva->addBoleto($boleto);
        }
    }
}

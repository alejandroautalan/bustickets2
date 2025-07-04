<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

use App\Entity\Pago;


final class PagoAdmin extends AbstractAdmin
{
    // public function configureRoutes(RouteCollectionInterface $collection): void
    // {
    //     $collection->add('asientos', 'asientos');
    //     $collection->add('procesar', 'procesar');
    //     $collection->add('ocuparAsiento', 'ocuparAsiento');
    //     $collection->add('setPasaje', 'setPasaje');
    //
    // }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('monto')
            ->add('fecha')
            ->add('tipo')
            ->add('observacion')
            ->add('usuario')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            #->add('id')
            ->add('monto')
            ->add('fecha')
            ->add('tipo', 'choice',[
                'choices' => Pago::$tipo_pagos,
                ])
            ->add('reserva')
            ->add('user')
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
        $form
            #->add('id')
            #->add('tipo', ChoiceType::class, [
            #    'choices' => Pago::getTipoPagoChoices(),
            #      'label' => 'Tipo'])
            #->add('numeroComprobante')
            #->add('monto', MoneyType::class, [
            #    'divisor' => 100,
            #    'disabled' => true,
            #    'currency' => 'ARS',
            #])
            ->add('monto', HiddenType::class)
            #->add('fecha', DatePickerType::class, Array('label'=>'Fecha', 'format'=>'d/M/y'))
            #->add('importeRecibido', MoneyType::class, [
            #    'divisor' => 100,
            #    'disabled' => true,
            #    'currency' => 'ARS',
            #    'label' => 'Importe a Pagar (un 10%)',
            #    'help' => 'El resto se abonara en efectivo antes de subir al colectivo'
            #])
            ->add('importeRecibido',HiddenType::class)
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('monto')
            ->add('fecha')
            ->add('tipo')
            ->add('observacion')
            ->add('usuario')
        ;
    }
}

<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;


final class BoletoAdmin extends BaseAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('viaje_fecha')
            ->add('viaje_hora')
            ->add('costo')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('viaje_fecha')
            ->add('viaje_hora')
            ->add('asiento.numero')
            ->add('costo')
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
            #->add('servicio')
            #->add('viaje_fecha')
            #->add('viaje_hora')
            ->add('asiento', null, ['disabled' => true])
            ->add('pasajero', ModelListType::class)
            ->add('costo', MoneyType::class, [
                'divisor' => 100,
                'currency' => 'ARS',
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('viaje_fecha')
            ->add('viaje_hora')
            ->add('asiento.numero')
            ->add('costo')
        ;
    }
}

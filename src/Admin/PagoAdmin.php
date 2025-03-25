<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Pasaje;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


final class PagoAdmin extends AbstractAdmin
{
    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('asientos', 'asientos');
        $collection->add('procesar', 'procesar');
        $collection->add('ocuparAsiento', 'ocuparAsiento');
        $collection->add('setPasaje', 'setPasaje');

    }

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
            ->add('id')
            ->add('monto')
            ->add('fecha')
            ->add('tipo')
            ->add('observacion')
            ->add('usuario')
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
            ->add('monto')
            ->add('fecha',DatePickerType::class, Array('label'=>'Fecha', 'format'=>'d/M/y'))
            ->add('tipo', ChoiceType::class,
            ['choices' => [
                'Transferencia' => 1,
                'Efectivo' => 2,
            ], 'label' => 'Tipo'])
            ->add('numeroComprobante')
            ->add('importeRecibido')
            ->add('pasajes', CollectionType::class, ['by_reference' => false,
                        'label' => 'Pasaje',
                        #'disabled' => $disabled,
                        'required'   => true,
                    ],
                        [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                ])
            #->add('observacion')
            #->add('usuario')
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

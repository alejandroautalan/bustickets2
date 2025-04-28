<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use App\Entity\Vehiculo;
use App\Form\Type\DependantEntityType;
use App\Form\EventSubscriber\AddDependantEntityFieldSubscriber;
use App\Configuration\DependantEntityConfig;


final class VehiculoAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('nombre')
            ->add('placa')
            ->add('is_active')
        ;
    }

    public function toString(object $object): string
    {
        return $object instanceof Vehiculo
        ? 'Vehículo'.$object->getNombre()
        : 'Vehículo'; // shown in the breadcrumb on the create view
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('nombre')
            ->add('placa')
            ->add('marca')
            ->add('modelo')
            ->add('is_active')
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
        $dependant_subscriber = new AddDependantEntityFieldSubscriber();
        $modelo_options = DependantEntityConfig::form_options('form_vehiculo:modelo_by_marca');
        $dependant_subscriber->addField('modelo', $modelo_options);

        $form
            #->add('id')
            ->add('nombre')
            ->add('placa')
            ->add('transporte')
            ->add('marca')
            ->add('modelo', DependantEntityType::class, $modelo_options)
            ->add('is_active')
        ;

        $builder = $form->getFormBuilder();
        $builder->addEventSubscriber($dependant_subscriber);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
            ->add('placa')
            ->add('marca')
            ->add('modelo')
            ->add('is_active')
        ;
    }
}

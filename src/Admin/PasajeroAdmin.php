<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PasajeroAdmin extends AbstractAdmin
{
    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('searchForDni', 'searchForDni');
    }
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            #->add('id')
            ->add('nombre')
            ->add('apellido')
            ->add('dni', null, ['show_filter' => true])
            #->add('edad')
            #->add('fecha_nacimiento')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            #->add('id')
            ->add('nombre')
            ->add('apellido')
            ->add('dni')
            #->add('edad')
            #->add('fecha_nacimiento')
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
            ->add('nombre')
            ->add('apellido')
            ->add('dni')
            ->add('sexo', ChoiceType::class, [
                'choices' => [
                    'Masculino' => 'M',
                    'Femenino' => 'F',
                ],
                'expanded' => false, // Opcional: Para mostrar los radios
                'multiple' => false, // Opcional: Para permitir múltiples selecciones
            ]);
            #->add('fecha_nacimiento')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
            ->add('apellido')
            ->add('dni')
            ->add('edad')
            ->add('fecha_nacimiento')
        ;
    }
}

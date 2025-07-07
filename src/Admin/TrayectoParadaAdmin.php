<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Trayecto;


final class TrayectoParadaAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('nro_orden')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('nro_orden', null, ['editable' => true,])
            ->add('parada.nombre', null, [
                'label'=> 'Parada'
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function alterNewInstance(object $object): void
    {
        $object->setTipoParadaId(Trayecto::TIPO_PARADA_DESTINO);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            #->add('id')
            ->add('nro_orden')
            //->add('nro_orden', HiddenType::class, [
            //    'required' => false,
            //    'label' => 'Orden',
            //    #'attr' => ['hidden' => true]
            //])
            ->add('tipo_parada_id', ChoiceType::class, [
                #'required'=> false,
                'label' => "Tipo",
                'choices' => Trayecto::getTiposParadaChoices(),
            ])
            ->add('parada', ModelListType::class)
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nro_orden')
            ->add('parada')
        ;
    }
}

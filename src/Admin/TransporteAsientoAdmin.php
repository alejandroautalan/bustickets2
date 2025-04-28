<?php

declare(strict_types=1);

namespace App\Admin;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;

use App\Entity\TransporteAsiento;


final class TransporteAsientoAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('numero')
            ->add('categoria')
        ;
    }

    public function toString(object $object): string
    {
        return $object instanceof TransporteAsiento
        ? 'Asiento N°'.$object->getNumero()
        : 'Asiento'; // shown in the breadcrumb on the create view
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, [
                  'header_style' => 'width: 5%;',
                  'row_align' => 'left'])
            ->add('numero', null, [
                'header_style' => 'width: 10%; text-align: center',
                'row_align' => 'center'])
            ->add('categoria', null, [
                'row_align' => 'center',
                'accessor' => function ($subject) {
                    return TransporteAsiento::$categorias[$subject->getCategoria()];
                }
            ])
            ->add('planta', FieldDescriptionInterface::TYPE_CHOICE, [
                'editable' => true,
                'choices' => TransporteAsiento::$plantas,
                'row_align' => 'center',
                'accessor' => function ($subject) {
                    return TransporteAsiento::$plantas[$subject->getPlanta()];
                }
            ])
            ->add('row', null, [
                'editable' => true,
                'header_style' => 'width: 5%; text-align: right',
                'row_align' => 'rigth'])
            ->add('col', null, [
                'editable' => true,
                'header_style' => 'width: 5%; text-align: right',
                'row_align' => 'rigth'])
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
            ->add('numero')
            ->add('categoria', ChoiceType::class, [
                'choices' => TransporteAsiento::getCategoriaChoices()
            ])
            ->add('planta', ChoiceType::class, [
                'choices' => TransporteAsiento::getPlantaChoices()
            ])
            ->add('row')
            ->add('col')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('numero')
            ->add('categoria')
        ;
    }
}

<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

use App\Entity\Boleto;


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
        $number_transformer = new MoneyToLocalizedStringTransformer(
            2, true, \NumberFormatter::ROUND_HALFUP, 100, 'es_AR');

        $list
            ->add('id', null, [
                'header_style' => 'width: 5%; text-align: left',
                'row_align' => 'left'])
            ->add('viaje_fecha', null, [
                'header_style' => 'width: 10%; text-align: center',
                'row_align' => 'center'])
            ->add('viaje_hora', null, [
                'header_style' => 'width: 10%; text-align: center',
                'row_align' => 'center'])
            ->add('asiento.numero', null, [
                'header_style' => 'width: 10%; text-align: center',
                'row_align' => 'center'])
            ->add('costo', FieldDescriptionInterface::TYPE_CURRENCY, [
                'currency' => 'ARS',
                'accessor' => function($subject) use($number_transformer) {
                    return $number_transformer->transform($subject->getCosto());
                },
                'header_style' => 'width: 10%; text-align: center',
                'row_align' => 'right'])
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
                'disabled' => true,
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => Boleto::getEstadoChoices(),
                'disabled' => true,
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

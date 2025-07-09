<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Form\Type\DependantEntityType;
use App\Form\EventSubscriber\AddDependantEntityFieldSubscriber;
use App\Configuration\DependantEntityConfig;
use App\Entity\ConfigPrecio;


final class ConfigPrecioAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('origen_provincia', null, ['label' => 'Provincia origen'])
            ->add('destino_provincia', null, ['label' => 'Provincia destino'])
            //->add('categoria_id')
            //->add('costo')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $number_transformer = new MoneyToLocalizedStringTransformer(
            2, true, \NumberFormatter::ROUND_HALFUP, 100, 'es_AR');
        $categorias = ConfigPrecio::getCategorias();

        $list
            ->add('id', null, ['header_style' => 'width: 5%; text-align: center',])
            ->add('origen', null, [
                'accessor' => function($subject) {
                    return $subject->getOrigenAsLabel();
                },
            ])
            ->add('destino', null, [
                'accessor' => function($subject) {
                    return $subject->getDestinoAsLabel();
                },
            ])
            ->add('categoria_id', null, [
                'label' => 'Categoría',
                'accessor' => function($subject) use($categorias) {
                    return $categorias[$subject->getCategoriaId()];
                },
            ])
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

    protected function alterNewInstance(object $object): void
    {
        $object->setCategoriaId(0);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $dependant_subscriber = new AddDependantEntityFieldSubscriber();

        $origen_ciudad_options = DependantEntityConfig::form_options('form-config-precio:origen-ciudad-by-provincia');
        $dependant_subscriber->addField('origen_ciudad', $origen_ciudad_options);
        $origen_parada_options = DependantEntityConfig::form_options('form-config-precio:origen-parada-by-ciudad');
        $dependant_subscriber->addField('origen_parada', $origen_parada_options);

        $destino_ciudad_options = DependantEntityConfig::form_options('form-config-precio:destino-ciudad-by-provincia');
        $dependant_subscriber->addField('destino_ciudad', $destino_ciudad_options);
        $destino_parada_options = DependantEntityConfig::form_options('form-config-precio:destino-parada-by-ciudad');
        $dependant_subscriber->addField('destino_parada', $destino_parada_options);

        $form
            ->tab('Configuración Precio')
                ->with('Origen', ['class' => 'col-md-4'])
                    //->add('id')
                    ->add('origen_provincia', null, ['label' => 'Provincia'])
                    ->add('origen_ciudad', DependantEntityType::class, $origen_ciudad_options)
                    ->add('origen_parada', DependantEntityType::class, $origen_parada_options)
                ->end()
                ->with('Destino', ['class' => 'col-md-4'])
                    ->add('destino_provincia', null, ['label' => 'Provincia'])
                    ->add('destino_ciudad', DependantEntityType::class, $destino_ciudad_options)
                    ->add('destino_parada', DependantEntityType::class, $destino_parada_options)
                ->end()
                ->with('Asiento', ['class' => 'col-md-8'])
                    ->add('categoria_id', ChoiceType::class, [
                        'choices' => ConfigPrecio::getCategoriaChoices(),
                        'label' => 'Categoría',
                    ])
                    ->add('costo', MoneyType::class, [
                        'divisor' => 100,
                        'currency' => 'ARS',
                    ])
                ->end()
            ->end()
        ;

        $builder = $form->getFormBuilder();
        $builder->addEventSubscriber($dependant_subscriber);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('categoria_id')
            ->add('costo')
        ;
    }
}

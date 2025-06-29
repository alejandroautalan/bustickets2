<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use App\Entity\Servicio;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use App\Admin\Extension\ServicioFUAdminExtension;

final class ServicioAdmin extends AbstractAdmin
{
    protected function isFinalUser(): bool
    {
        $is_superadmin = $this->isGranted('ROLE_SUPER_RADMIN');
        $is_finaluser = $this->isGranted('ROLE_FINAL_USER');
        return (!$is_superadmin and $is_finaluser);
    }

    protected function configure(): void
    {
        if ($this->isFinalUser()) {
            $this->addExtension(new ServicioFUAdminExtension());
        }
    }

    protected function configureBatchActions(array $actions): array
    {
        return [];
    }

    public function showBtnBoletos(): bool
    {
        if($this->isFinalUser())
            return False;
        return True;
    }

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('reserva', 'reserva');
        $collection->add('asientos', 'asientos');
        $collection->add('archivo', 'archivo');
        $collection->add('ocuparAsiento', 'ocuparAsiento');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $minAttr = [];
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $minDate = date('Y-m-d');
            $minAttr = ['min' => $minDate];
        }
        $filter
            ->add('trayecto.origen', null, ['label' => 'Origen'])
            ->add('trayecto.destino', null, ['label' => 'Destino'])
            #->add('nombre')
            ->add('partida', DateFilter::class, [
                'field_type' => DateType::class,
                'field_options' => [
                    'widget' => 'single_text',
                    'html5' => true,       // Desactiva el picker nativo HTML5 para poder usar formato personalizado
                    #'format' => 'yyyy-MM-dd HH:mm:ss',
                    'attr' => array_merge(['class' => 'form-control'], $minAttr),
                ],
            ])
            ->add('llegada', DateFilter::class, [
                'field_type' => DateType::class,
                'field_options' => [
                    'widget' => 'single_text',
                    'html5' => true,       // Desactiva el picker nativo HTML5 para poder usar formato personalizado
                    #'format' => 'yyyy-MM-dd HH:mm:ss',
                    'attr' => array_merge(['class' => 'form-control'], $minAttr),
                ],
            ])
            #->add('llegada')
            #->add('estado')
        ;
    }

    public function toString(object $object): string
    {
        return $object instanceof Servicio
        ? 'Servicio N°'.$object->getId()
        : 'Servicio'; // shown in the breadcrumb on the create view
    }

    protected function configureListFields(ListMapper $list): void
    {
        $actions = [
            #'show' => [],
            'edit' => [],
            'delete' => [],
            #'reserva' => ['template' => 'ServicioAdmin/reserva_list_btn.html.twig'],
            #'asientos' => ['template' => 'ServicioAdmin/asientos_ocupacion.html.twig'],
            'archivo' => ['template' => 'ServicioAdmin/archivo.html.twig'],
            'boletos'  => ['template' => 'ServicioAdmin/boletos.html.twig'],
        ];

        $list
            #->add('id')
            ->add('nombreTrayecto',  null, ['template' => 'ServicioAdmin/trayecto.html.twig', 'label' => 'Servicio']);

        #if(!$this->isFinalUser()):
        #    $list->add(ListMapper::NAME_ACTIONS, null, [
        #        'actions' => $actions,
        #    ]);
        #endif;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            #->add('id')
            ->add('nombre')
            ->add('trayecto')
            ->add('transporte')
            ->add('vehiculo')
            ->add('partida', null, [
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('llegada', null, [
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => Servicio::$estado_choices
            ])
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
            ->add('nombre')
            ->add('partida')
            ->add('llegada')
            ->add('estado')
        ;
    }

    protected function configureTabMenu(MenuItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        if ($this->isGranted('LIST')) {
            $menu->addChild('Boletos', $admin->generateMenuUrl('admin.boleto.list', ['id' => $id]));
        }
    }
}

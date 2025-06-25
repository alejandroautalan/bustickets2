<?php

declare(strict_types=1);

namespace App\Admin;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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


final class ServicioAdmin extends AbstractAdmin
{

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('reserva', 'reserva');
        $collection->add('asientos', 'asientos');
        $collection->add('archivo', 'archivo');
        $collection->add('ocuparAsiento', 'ocuparAsiento');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            #->add('id')
            ->add('nombre')
            ->add('partida')
            ->add('llegada')
            ->add('estado')
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
            'show' => [],
            'edit' => [],
            'delete' => [],
            'reserva' => ['template' => 'ServicioAdmin/reserva_list_btn.html.twig'],
            #'asientos' => ['template' => 'ServicioAdmin/asientos_ocupacion.html.twig'],
            'archivo' => ['template' => 'ServicioAdmin/archivo.html.twig'],
            'boletos'  => ['template' => 'ServicioAdmin/boletos.html.twig'],
        ];

        $list
            #->add('id')
            ->add('nombre',  null, ['label' => 'Servicio'])
            ->add('partida')
            ->add('llegada')
            #->add('estado', null, )
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => $actions,
            ]);
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

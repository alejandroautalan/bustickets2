<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;


final class ColectivoAdmin extends AbstractAdmin
{


    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('nombre')
            ->add('patente')
            ->add('activo')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $actions = [
            'show' => [],
            'edit' => [],
            'delete' => [],
            'asientos' => [
                'template' => 'ColectivoAdmin/show_asientos.html.twig'
            ],
        ];
        
        $list
            ->add('id')
            ->add('nombre')
            ->add('patente')
            ->add('modelo')
            ->add('activo')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => $actions
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            #->add('id')
            ->add('nombre')
            ->add('patente')
            ->add('modelo')
            ->add('activo')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
            ->add('patente')
            ->add('modelo')
            ->add('activo')
        ;
    }
    
    protected function configureTabMenu(MenuItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild('View', $admin->generateMenuUrl('show', ['id' => $id]));

        if ($this->isGranted('EDIT')) {
            $menu->addChild('Edit', $admin->generateMenuUrl('edit', ['id' => $id]));
        }

        if ($this->isGranted('LIST')) {
            $menu->addChild('Asientos', $admin->generateMenuUrl('admin.asiento.list', ['id' => $id]));
        }
    }
}

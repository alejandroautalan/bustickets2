<?php

declare(strict_types=1);

namespace App\Admin;

use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;


final class ProvinciaAdmin extends AbstractAdmin
{
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('nombre')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('nombre')
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
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
        ;
    }

    protected function configureTabMenu(MenuItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }
        // $this_admin = get_class($this);
        // $parent_admin = $this->isChild() ? get_class($this->getParent()) : 'None';
        // $msg = "this:".$this_admin.' Parent: '.$parent_admin."\n";
        // $this->logger->error($msg);


        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        // $menu->addChild('Provincia View', $admin->generateMenuUrl('show', ['id' => $id]));
        //
        // if ($this->isGranted('EDIT')) {
        //     $menu->addChild('Provincia Edit', $admin->generateMenuUrl('edit', ['id' => $id]));
        // }

        $tiene_ciudad = $this->hasChild('admin.ciudad');
        $ciudad_admin = $tiene_ciudad? $this->getChild('admin.ciudad'): null;
        if($tiene_ciudad) {
            if($ciudad_admin->hasSubject()) {
                $ciudad = $ciudad_admin->getSubject();
                $menu->addChild('Paradas', $ciudad_admin->generateMenuUrl(
                    'admin.parada.list', [
                        'id' => $ciudad->getId(),
                    ]));
            } else {
                if ($ciudad_admin->isGranted('LIST')) {
                    $menu->addChild('Ciudades', $admin->generateMenuUrl('admin.ciudad.list', ['id' => $id]));
                }
            }
        }
    }
}

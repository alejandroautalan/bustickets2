<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\Form\Type\CollectionType;

use App\Entity\Transporte;
use App\Entity\TransporteAsiento;
use App\Admin\Extension\CloneActionAdminExtension;


final class TransporteAdmin extends AbstractAdmin
{
    protected function configure(): void
    {
        $this->addExtension(new CloneActionAdminExtension());
    }

    public function setupCloneFrom(Transporte $object, $clone_from_id)
    {
        $clone_from = $this->getObject($clone_from_id);
        $object->setNombre(sprintf('%s - Copia', $clone_from->getNombre()));
        $object->setGrillaRows($clone_from->getGrillaRows());
        $object->setGrillaCols($clone_from->getGrillaCols());
        $object->setPlantas($clone_from->getPlantas());

        foreach($clone_from->getAsientos() as $child) {
            $new_child = clone $child;
            $object->addAsiento($new_child);
        }
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
            ->add('plantas')
            ->add('grilla_rows')
            ->add('grilla_cols')
            ->add('asientos', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Asientos',
                'required' => false,
            ],
            [
                'edit' => 'inline',
                'inline' => 'table',
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
            ->add('plantas')
            ->add('grilla_rows')
            ->add('grilla_cols')
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
            $menu->addChild('Asientos', $admin->generateMenuUrl('admin.transporte_asiento.list', ['id' => $id]));
        }
    }
}

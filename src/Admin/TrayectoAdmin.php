<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;

use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;

use App\Entity\Trayecto;
use App\Entity\TrayectoParada;
use App\Admin\Extension\CloneActionAdminExtension;



final class TrayectoAdmin extends AbstractAdmin
{
    protected function configure(): void
    {
        $this->addExtension(new CloneActionAdminExtension());
    }

    public function setupCloneFrom(Trayecto $object, $clone_from_id)
    {
        $clone_from = $this->getObject($clone_from_id);
        //$object->setEnabled($clone_from->isEnabled());
        $object->setEnabled(false);
        $object->setNombre(sprintf('%s - Copia', $clone_from->getNombre()));
        foreach($clone_from->getTrayectoParadas() as $tp) {
            $tpnew = clone $tp;
            $object->addTrayectoParada($tpnew);
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
            ->add('origen')
            ->add('destino')
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
        $trayecto = $this->getSubject();
        $is_new = $trayecto->getId() == null;
        $form
            #->add('id')
            ->add('nombre')
            ->add('enabled', null, ['label' => 'Activo'])
            ->add('trayectoParadas', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Puntos del Trayecto',
                'required' => false,
            ],
            [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'nro_orden',
#                'limit' => 3,
            ])
            //->ifFalse($is_new)
            //->add('origen', null, ["disabled" => true, 'required' => false])
            //->add('destino', null, ["disabled" => true, 'required' => false])
            //->ifEnd()
            ;
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
            ->add('origen')
            ->add('destino')
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
            $menu->addChild('Puntos', $admin->generateMenuUrl('admin.trayecto_parada.list', ['id' => $id]));
        }
    }
}

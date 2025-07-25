<?php

declare(strict_types=1);

namespace App\Admin\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
#use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/*
#[AutoconfigureTag(name: 'sonata.admin.extension', attributes: ['target' => 'sonata.page.admin.page'])]
*/
final class ServicioFUAdminExtension extends AbstractAdminExtension
{
    public function configureListFields(ListMapper $list): void
    {
        $list->remove('id')
            ->remove('estado')
        ;
        #$actionsfd = $list->get(ListMapper::NAME_ACTIONS);
        #$options = $actionsfd->getOptions();
        #$actions = $options['actions'];
        #unset($actions['show']);
        #unset($actions['reserva']);
        #unset($actions['archivo']);
        #unset($actions['asientos']);
    }

    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query): void
    {
        parent::configureQuery($admin, $query);
        $now = new \DateTime();
        $today = new \DateTime('today midnight');  // o 'today' si quieres más precisión
        $query->andWhere($query->getRootAlias()[0].'.partida >= :now')
            ->setParameter('now', $now);
    }

    public function configureRoutes(AdminInterface $admin, RouteCollectionInterface $collection): void
    {
        $collection->remove('show')
            #->remove('reserva')
            ->remove('archivo')
            ->remove('asientos')
        ;
    }

}

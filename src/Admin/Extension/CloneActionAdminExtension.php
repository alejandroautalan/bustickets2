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
final class CloneActionAdminExtension extends AbstractAdminExtension
{
    public function configureRoutes(AdminInterface $admin, RouteCollectionInterface $collection): void
    {
        $collection->add('objclone');
    }

    public function configureActionButtons(
        AdminInterface $admin,
        array $list,
        string $action,
        ?object $object = null,
    ): array {

        return $list;
    }

    public function configureListFields(ListMapper $list): void
    {
        $actionsfd = $list->get(ListMapper::NAME_ACTIONS);
        $options = $actionsfd->getOptions();
        $actions = $options['actions'];

        $actions['objclone'] = [
            'template' => 'AdminExtension/objclone_list_btn.html.twig',
        ];
        //print_r($actions); die();
    }

    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query): void
    {
    }

}

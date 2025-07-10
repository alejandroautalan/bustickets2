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
    public function configureListFields(ListMapper $list): void
    {
        $actionsfd = $list->get(ListMapper::NAME_ACTIONS);
        $options = $actionsfd->getOptions();
        $actions = $options['actions'];
        $actions['objclone'] = [
            'template' => 'AdminExtension/objclone_list_btn.html.twig',
        ];
        $actionsfd->setOption('actions', $actions);
    }

    public function alterNewInstance(AdminInterface $admin, object $object): void
    {
        $clone_from_id = $admin->getRequest()->get('clone_from_id', null);
        if(null !== $clone_from_id) {
            $admin->setupCloneFrom($object, $clone_from_id);
        }
    }

}

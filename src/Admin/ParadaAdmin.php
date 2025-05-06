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

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Entity\Ciudad;
use App\Entity\Provincia;
use App\Form\Type\DependantEntityType;
use App\Form\EventSubscriber\AddDependantEntityFieldSubscriber;
use App\Configuration\DependantEntityConfig;


final class ParadaAdmin extends AbstractAdmin
{
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
            ->add('ciudad')
            ->add('provincia')
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
        $dependant_subscriber = new AddDependantEntityFieldSubscriber();
        $city_options = DependantEntityConfig::form_options('ciudad_by_provincia');
        $dependant_subscriber->addField('ciudad', $city_options);

        $form
            #->add('id')
            ->add('nombre')
        ;
        if(!$this->isChild()) {
            $form->add('provincia')
                ->add('ciudad', DependantEntityType::class, $city_options)
                ;
            $builder = $form->getFormBuilder();
            $builder->addEventSubscriber($dependant_subscriber);
        }
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nombre')
        ;
    }

    // protected function configureRoutes(RouteCollectionInterface $collection): void
    // {
    //     if ($this->isChild()) {
    //         return;
    //     }
    //
    //     // This is the route configuration as a parent
    //     $collection->clear();
    //
    // }
}

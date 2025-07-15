<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Pago;
use App\Entity\Parada;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;

use App\Entity\Servicio;
use App\Entity\ConfigPrecio;
use App\Admin\Extension\ServicioFUAdminExtension;
use App\Form\Type\DependantEntityType;
use App\Form\EventSubscriber\AddDependantEntityFieldSubscriber;
use App\Configuration\DependantEntityConfig;
use Symfony\Component\Validator\Constraints\Choice;


final class ServicioAdmin extends AbstractAdmin
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }
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

    public function getServicioCosto($origen_parada, $destino_parada) {
        $repo_parada = $this->getModelManager()
            ->getEntityManager(Parada::class)
            ->getRepository(Parada::class)
            ;
        $repo_cp = $this->getModelManager()
            ->getEntityManager(ConfigPrecio::class)
            ->getRepository(ConfigPrecio::class)
            ;
        if(! $origen_parada instanceof Parada) {
            $origen_parada = $repo_parada->find($origen_parada);
        }
        if(! $destino_parada instanceof Parada) {
            $destino_parada = $repo_parada->find($destino_parada);
        }
        $precio = $repo_cp->getCosto($origen_parada, $destino_parada);
        return $precio;
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        if ($this->isFinalUser()) {
            $filters = $this->getFilterParameters();
            if (isset($filters['origen']) && isset($filters['destino'])) {
                $filterParadaO = $filters['origen']['value'];
                $filterParadaD = $filters['destino']['value'];

                if ($filterParadaO !== '' && $filterParadaO !== '') {
                    $query->join($query->getRootAlias() . '.trayecto', 't')
                        ->join('t.trayectoParadas', 'tp_origen', 'WITH', 'tp_origen.parada = :origen ')
                        ->join('t.trayectoParadas', 'tp_destino', 'WITH', 'tp_destino.parada = :destino ')
                        ->where('tp_origen.nro_orden < tp_destino.nro_orden')
                        ->andWhere('tp_origen.tipo_parada_id = 1')
                        ->andWhere('tp_destino.tipo_parada_id = 2')
                        ->andWhere($query->getRootAlias() . '.estado = 2')
                        ->setParameter('origen', $filterParadaO)
                        ->setParameter('destino', $filterParadaD);
                }
            } else {
                $query->where($query->getRootAlias() . '.id = -100');
            }
        } elseif($this->isGranted('ROLE_SUPER_ADMIN')){
            $filters = $this->getFilterParameters();
            if (isset($filters['origen']) && isset($filters['destino'])) {
                $filterParadaO = $filters['origen']['value'];
                $filterParadaD = $filters['destino']['value'];

                if ($filterParadaO !== '' && $filterParadaO !== '') {
                    $query->join($query->getRootAlias() . '.trayecto', 't')
                        ->join('t.trayectoParadas', 'tp_origen', 'WITH', 'tp_origen.parada = :origen ')
                        ->join('t.trayectoParadas', 'tp_destino', 'WITH', 'tp_destino.parada = :destino ')
                        ->where('tp_origen.nro_orden < tp_destino.nro_orden')
                        ->andWhere('tp_origen.tipo_parada_id = 1')
                        ->andWhere('tp_destino.tipo_parada_id = 2')
                        ->setParameter('origen', $filterParadaO)
                        ->setParameter('destino', $filterParadaD);
                }
            }
        }
        return $query;
    }

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('reserva', 'reserva');
        $collection->add('asientos', 'asientos');
        $collection->add('archivo', 'archivo');
        $collection->add('ocuparAsiento', 'ocuparAsiento');
        $collection->add('autocomplete_items');
    }

    private function getParadasChoices(): array
    {
        $em = $this->entityManager;

        $paradas = $em->getRepository(\App\Entity\Parada::class)->findAll();

        $choices = [];
        foreach ($paradas as $parada) {
            // La clave es la etiqueta visible, el valor es lo que usará el filtro
            $choices[$parada->getNombre()] = $parada->getId();
        }

        return $choices;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $minAttr = [];
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $minDate = date('Y-m-d');
            $minAttr = ['min' => $minDate];
        }
        $filter
            ->add('origen', CallbackFilter::class, [
                'required' => true,
                'callback' => [$this, 'filterby_origen'],
                'show_filter' => true,
                'field_type' => ModelAutocompleteType::class,
                'field_options' => [
                    'class' => Parada::class,
                    'placeholder' => 'Selecciona origen',
                    'required' => true,
                    'property' => 'nombre',
                    'route' => ['name' => 'admin_app_servicio_autocomplete_items', 'parameters' => []],
                ]
            ])
            // Filtro por destino
            ->add('destino', CallbackFilter::class, [
                'required' => true,
                'callback' => [$this, 'filterby_destino'],
                'show_filter' => true,
                'field_type' => ModelAutocompleteType::class,
                'field_options' => [
                    'class' => Parada::class,
                    'placeholder' => 'Selecciona destino',
                    'required' => true,
                    'property' => 'nombre',
                    'route' => ['name' => 'admin_app_servicio_autocomplete_items', 'parameters' => []],
                ]
            ])
            ->add('partida', DateFilter::class, [
                'show_filter' => true,
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

    public function filterby_origen(ProxyQueryInterface $query, string $alias, string $field, FilterData $data) {
        if (!$data->hasValue()) {
            return false;
        }
        // $query is setup in configureQuery
        return true;
    }

    public function filterby_destino(ProxyQueryInterface $query, string $alias, string $field, FilterData $data) {
        if (!$data->hasValue()) {
            return false;
        }
        // $query is setup in configureQuery
        return true;
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

        if($this->isFinalUser()):
        $list
            #->add('id')
            ->add('nombreTrayecto',  null, ['template' => 'ServicioAdmin/trayecto.html.twig', 'label' => 'Servicio']);
        endif;

        if(!$this->isFinalUser()):
            $list
                ->add('id')
                ->add('trayecto.origen')
                ->add('trayecto.destino')
                ->add('partida')
                ->add('llegada')
                ->add('asientosLibres', null, ['lable'=>'Libres'])
                ->add('estado', 'choice',[
                    'choices' => Servicio::$estado_nombre_choices,
                ])
                ->add('costo', null, ['template' => 'ServicioAdmin/costos.html.twig'])
                ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => $actions,
            ]);
        endif;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $dependant_subscriber = new AddDependantEntityFieldSubscriber();
        $vehiculo_options = DependantEntityConfig::form_options('form_servicio:vehiculo_by_transporte');
        $dependant_subscriber->addField('vehiculo', $vehiculo_options);

        $form
            #->add('id')
            ->add('nombre')
            ->add('trayecto')
            ->add('transporte')
            ->add('vehiculo', DependantEntityType::class, $vehiculo_options)
            ->add('fecha', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('partida', null, [
                // renders it as a single text box
                'widget' => 'single_text',
                'disabled' => true,
                'required' => false,
            ])
            ->add('llegada', null, [
                // renders it as a single text box
                'widget' => 'single_text',
                'disabled' => true,
                'required' => false,
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => Servicio::$estado_choices
            ])
            ->add('costo', MoneyType::class, [
                'divisor' => 100,
                'currency' => 'ARS',
            ])
        ;
        $builder = $form->getFormBuilder();
        $builder->addEventSubscriber($dependant_subscriber);
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

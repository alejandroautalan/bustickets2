<?php
namespace App\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use App\Entity\Ciudad;
use App\Entity\Provincia;
use App\Form\Type\DependantEntityType;
use App\Configuration\DependantEntityConfig;


class AddDependantEntityFieldSubscriber implements EventSubscriberInterface
{
    protected $property_accessor;
    protected $form_options;
    protected $fields;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->form_options = null;
        $this->fields = array();
    }

    public function addField($name, $options) {
        $this->fields[$name] = $options;
    }

    public static function getSubscribedEvents(): array
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = $this->propertyAccessor;
        foreach($this->fields as $name => $options_original) {
            $options = array_merge([], $options_original);
            $object = $data;

            $obj_parent_field = $options['parent_entity_field'];

            if(isset($options['parent_form_field'])) {
                $obj_parent_field = $options['parent_form_field'];
            }
            $parent = ($object) ? $accessor->getValue($object, $obj_parent_field): null;
            $parent_id = ($parent)? $parent->getId() : null;

            $obj_field_name = $options['entity_field'];
            if(isset($options['form_field'])) {
                $obj_field_name = $options['form_field'];
            }
            $field_value = ($object) ? $accessor->getValue($object, $obj_field_name): null;

            $this->addDependantForm($form, $parent_id, $obj_field_name, $field_value, $options);
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        foreach($this->fields as $name => $options) {
            $parent_field = $options['parent_entity_field'];
            if(isset($options['parent_form_field'])) {
                $parent_field = $options['parent_form_field'];
            }
            $parent_id = array_key_exists($parent_field, $data) ? $data[$parent_field] : null;

            $field_name = $options['entity_field'];
            if(isset($options['form_field'])) {
                $field_name = $options['form_field'];
            }
            $field_value = array_key_exists($field_name, $data) ? $data[$field_name] : null;

            $this->addDependantForm($form, $parent_id, $field_name, $field_value, $options);
        }
    }

    private function addDependantForm($form, $parent_id, $field_name, $field_value, $options)
    {
        $parent_property = $options['parent_entity_field'];
        #$field_name = $options['entity_field'];

        $order_field = null;
        $order_dir = 'ASC';
        $search_callback = null;
        if(array_key_exists('search_order_field', $options))
            $order_field = $options['search_order_field'];
        if(array_key_exists('search_order_direction', $options))
            $order_dir = $options['search_order_direction'];
        if(array_key_exists('search_callback', $options))
            $search_callback = $options['search_callback'];

        $filter_callback = null;
        //$doctrine_em = $this->container->get('doctrine');

        if (null !== $search_callback) {
            $filter_callback = $search_callback;
            //$repository = $doctrine_em->getRepository($options['class']);

            //if (!method_exists($repository, $filter_callback)) {
            //    $msg = 'Callback function "%s" in Repository "%s" does not exist. For class %s';
            //    $msg = sprintf($msg, $filter_callback,
            //                   get_class($repository), $options['class']);
            //    throw new \InvalidArgumentException($msg);
            //}
        }

        $query_builder = function (EntityRepository $repository)
        use ($parent_id, $parent_property,
             $order_dir, $order_field,
             $field_name, $field_value, $filter_callback) {

            $qb = null;

            if(null !== $filter_callback) {
                # Custom filtering, you must do all filtering
                $qb = $repository->$filter_callback($parent_id, $field_value);
            } else {
                # Default filter: filter by parent
                # if field_value is not null, include that value.
                $qb = $repository->createQueryBuilder('d')
                ->where('d.' . $parent_property . ' = :parent_id')
                ->setParameter('parent_id', $parent_id)
                ;
                if(null !== $field_value) {
                    $qb->orWhere('d = :field_value')
                    ->setParameter(':field_value', $field_value)
                    ;
                }

                if(null !== $order_field) {
                    $qb->addOrderBy('d.'.$order_field, $order_dir);
                }
            }
            return $qb;
             };

             $options['query_builder'] = $query_builder;

             $form->add($field_name, DependantEntityType::class, $options);
    }

    private function addCityForm(FormInterface $form, $provincia_id = null): void {

        $form->add('ciudad', DependantEntityType::class, [
            'form_field' => 'esta_te_doy',
            'class' => Ciudad::class,
            'placeholder' => '',
            'query_builder' => function (EntityRepository $repository) use ($provincia_id) {
                $qb = $repository->createQueryBuilder('ciudad')
                ->innerJoin('ciudad.provincia', 'provincia')
                ->where('provincia.id = :provincia')
                ->setParameter(':provincia', $provincia_id)
                ;

                return $qb;
            }
        ]);
    }

    public function preSetData2(FormEvent $event): void {
        $form = $event->getForm();
        #$conf = $form->getConfig()->getOptions();
        #echo print_r($conf['form_field']); die();
        // this would be your entity, i.e. SportMeetup
        $data = $event->getData();
        if (null === $data) {
            return;
        }
        # echo print_r($data), die();
        $city = $data->getCiudad();
        $province_id = ($city) ? $city->getProvincia()->getId() : null;
        $this->addCityForm($form, $province_id);
    }

    public function preSubmit2(FormEvent $event): void {
        //$form = $event->getForm();
        // It's important here to fetch $event->getForm()->getData(), as
        // $event->getData() will get you the client data (that is, the ID)
        //$mainobject = $form->getData();
        //$provincia = $mainobject->getProvincia();

        $data = $event->getData();
        $form = $event->getForm();
        $province_id = array_key_exists('provincia', $data) ? $data['provincia'] : null;
        $this->addCityForm($form, $province_id);
    }
}

<?php
namespace App\Form\EventSubscriber;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use App\Entity\Ciudad;


class AddCiudadFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT    => 'preSubmit'
        ];
    }

    private function addCityForm($form, $province_id)
    {
        $formOptions = array(
            'class'         => Ciudad::class,
            'empty_value'   => 'Ciudad',
            'label'         => 'Ciudad',
            'attr'          => array(
                'class' => 'city_selector',
            ),
            'query_builder' => function (EntityRepository $repository) use ($province_id) {
                $qb = $repository->createQueryBuilder('city')
                ->innerJoin('city.provincia', 'p')
                ->where('p.id = :provincia')
                ->setParameter('provincia', $province_id)
                ;

                return $qb;
            }
        );

        $form->add($this->propertyPathToCity, 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor    = PropertyAccess::createPropertyAccessor();
        $property_path_to_city = 'ciudad';
        #$form_options = $form->getConfig()->getOptions)();
        $city        = $accessor->getValue($data, $property_path_to_city);
        $province_id = ($city) ? $city->getProvince()->getId() : null;

        $this->addCityForm($form, $province_id);
    }
}

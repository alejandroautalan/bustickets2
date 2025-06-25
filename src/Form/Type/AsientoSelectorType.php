<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AsientoSelectorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'x' => false,
        ]);

        # class option is required by base class
        $resolver->setRequired(array(
            'transporte', #
            'asientos_libres', # numeros de asientos libres
            'asientos_reserva',
            'idreserva',
        ));
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setAttribute("transporte", $options['transporte']);
        $builder->setAttribute("asientos_libres", $options['asientos_libres']);
        $builder->setAttribute("idreserva", $options['idreserva']);
        $builder->setAttribute("asientos_reserva", $options['asientos_reserva']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['transporte'] = $form->getConfig()->getAttribute('transporte');
        $view->vars['idreserva'] = $form->getConfig()->getAttribute('idreserva');
        $view->vars['asientos_libres'] = $form->getConfig()->getAttribute('asientos_libres');
        $view->vars['asientos_reserva'] = $form->getConfig()->getAttribute('asientos_reserva');
    }
}

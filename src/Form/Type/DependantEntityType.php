<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DependantEntityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            #'config_name' => null,
            #'parent_entity_field' => null,
            'parent_form_field' => null,
            #'entity_field' => null,
            'form_field' => null,
            'search_order_field' => null,
            'search_order_direction' => null,
            'search_callback' => null,

            'compound'     => false,
            'placeholder'  => '',
            #'choice_label' => null,
            'no_result_msg' => '',
        ]);

        # class option is required by base class
        $resolver->setRequired(array(
            'config_name', # Configuration Id, setup in app/config/application_tools
            'entity_field', # Attribute of the class
            'parent_entity_field', # Attribute of the parent class
        ));
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $value = $options['parent_form_field'];
        $value = (null === $value)? $options['parent_entity_field']: $value;
        $builder->setAttribute("parent_form_field", $value);

        $value = $options['form_field'];
        $value = (null === $value)? $options['entity_field']: $value;
        $builder->setAttribute("form_field", $value);

        $builder->setAttribute("config_name", $options['config_name']);
        $builder->setAttribute("no_result_msg", $options['no_result_msg']);
        $builder->setAttribute("placeholder", $options['placeholder']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['form_field'] = $form->getConfig()->getAttribute('form_field');
        $view->vars['parent_form_field'] = $form->getConfig()->getAttribute('parent_form_field');
        $view->vars['config_name'] = $form->getConfig()->getAttribute('config_name');
        $view->vars['no_result_msg'] = $form->getConfig()->getAttribute('no_result_msg');
        $view->vars['placeholder'] = $form->getConfig()->getAttribute('placeholder');
    }
}

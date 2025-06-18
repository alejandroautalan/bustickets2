<?php
namespace App\Form\Type;

use App\Entity\Pasajero;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasajeroType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('nombre', TextType::class, [
            'label' => 'Nombre',
        ])
        ->add('apellido', TextType::class, [
            'label' => 'Apellido',
        ])
        ->add('dni', TextType::class, [
            'label' => 'DNI',
        ])
        ->add('sexo', ChoiceType::class, [
            'label' => 'Sexo',
            'choices' => [
                'Masculino' => 'M',
                'Femenino' => 'F',
                'Otro' => 'O',
            ],
            'placeholder' => 'Seleccione...', // Opcional, añade una opción vacía
            'expanded' => false, // false = dropdown, true = radio buttons
            'multiple' => false, // false = una selección, true = múltiples
        ]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Pasajero::class,
]);
}
}
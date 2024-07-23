<?php

namespace App\Form;

use App\Entity\colmenar\FutTRivales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FutTRivalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
        $builder
            ->add('rival', null, [
                'label' => 'Nombre del Rival',
            ])
            ->add('camiseta', TextType::class, [
                'label' => 'Camiseta',
                'required' => false, // Permite valores nulos
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('pantalon', TextType::class, [
                'label' => 'Pantalón',
                'required' => false, // Permite valores nulos
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('medias', TextType::class, [
                'label' => 'Medias',
                'required' => false, // Permite valores nulos
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('comprobada_equipacion', ChoiceType::class, [
                'label' => 'Comprobada Equipación',
                'choices' => [
                    'SI' => 'SI',
                    'NO' => 'NO',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('cod_eq_fed', null, [
                'label' => 'Código Federación',
            ])
            ->add('nombre_eq_fed', null, [
                'label' => 'Nombre Federación',
            ])
            ->add('localidad_fed', null, [
                'label' => 'Localidad Federación',
            ])
            ->add('provincia_fed', null, [
                'label' => 'Provincia Federación',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FutTRivales::class,
        ]);
    }
}

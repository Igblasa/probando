<?php

namespace App\Form;

use App\Entity\colmenar\FutTEstadios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FutTEstadiosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre_estadio')
            ->add('direccion')
            ->add('codigo_fed')
            ->add('nombre_fed')
            ->add('direccion_fed')
            ->add('codigo_postal_fed')
            ->add('provincia_fed')
            ->add('localidad_fed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FutTEstadios::class,
        ]);
    }
}

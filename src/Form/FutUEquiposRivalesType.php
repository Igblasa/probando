<?php

namespace App\Form;

use App\Entity\colmenar\FutUEquiposRivales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class FutUEquiposRivalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_equipo', EntityType::class, [
                'class' => 'App\Entity\colmenar\FutTEquipos',
                'choice_label' => 'equipo',
                'attr' => ['class' => 'form-control'],
                'label' => 'Equipo',
                'required' => true,
                'em' => 'colmenar',
            ])
            ->add('id_rival', EntityType::class, [
                'class' => 'App\Entity\colmenar\FutTRivales',
                'choice_label' => 'rival',
                'attr' => ['class' => 'form-control'],
                'label' => 'Rival',
                'required' => true,
                'em' => 'colmenar',
            ])
            ->add('id_estadio', EntityType::class, [
                'class' => 'App\Entity\colmenar\FutTEstadios',
                'choice_label' => 'nombreEstadio',
                'attr' => ['class' => 'form-control'],
                'label' => 'Estadio',
                'em' => 'colmenar',
            ])
            ->add('id_detalle_tipo_partido', EntityType::class, [
                'class' => 'App\Entity\colmenar\FutTDetalleTipoPartido',
                'choice_label' => 'nombreDetalle',
                'attr' => ['class' => 'form-control'],
                'label' => 'Tipo de partido',
                'required' => true,
                'em' => 'colmenar',
            ])
            ->add('fecha', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('horario', TimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])

            ->add('resultado_local', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('resultado_visitante', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('local', ChoiceType::class, [
                'choices' => [
                    'Sí' => 'si',
                    'No' => 'no',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('observaciones', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('mostrar', ChoiceType::class, [
                'choices' => [
                    'Sí' => 1,
                    'No' => 0,
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'HH:mm'
                ]
            ])
            ->add('autocar', null, [
                'attr' => [
                    'class' => 'form-control col-lg-3',
                    'placeholder' => 'HH:mm',
                ],
            ])      

            ->add('codigoPartido', null, [
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FutUEquiposRivales::class,
        ]);
    }
}

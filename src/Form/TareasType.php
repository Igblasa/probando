<?php

namespace App\Form;

use App\Entity\colmenar\IntTareas;
use App\Entity\colmenar\IntCategorias;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TareasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', null, [
                'label' => 'Título',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('descripcion', null, [
                'label' => 'Descripción',
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('estado_tarea', ChoiceType::class, [
                'choices' => [
                    'Abierta' => 'Abierta',
                    'Pausada' => 'Pausada',
                    'Cerrada' => 'Cerrada',
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Estado de la tarea',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fichero', FileType::class, [
                'label' => 'Fichero',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control-file'],
            ])
            ->add('categoria', EntityType::class, [
                'class' => IntCategorias::class,
                'choice_label' => 'categoria',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Categoría',
                'attr' => ['class' => 'form-control'],
            ]);

        if ($options['is_edit']) {
            $builder
                ->remove('fecha_de_creacion')
                ->remove('creador')
                ->remove('fecha_de_modificacion');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IntTareas::class,
            'is_edit' => false,
        ]);
    }
}

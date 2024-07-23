<?php

namespace App\Form;

use App\Entity\IntComentarios;
use App\Entity\IntTareas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ComentariosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha_de_creacion')
            ->add('creador')
            ->add('fecha_de_modificacion')
            ->add('descripcion')
            ->add('tarea', EntityType::class, [
                'class' => IntTareas::class,
                'choice_label' => 'titulo', // Asumiendo que 'titulo' es el campo de la tarea que deseas mostrar
                'multiple' => false,
                'expanded' => false,
            ])
        ;
        
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
            'data_class' => IntComentarios::class,
            'is_edit' => false,
        ]);
    }
}

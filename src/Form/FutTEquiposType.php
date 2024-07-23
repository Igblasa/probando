<?php

namespace App\Form;

use App\Entity\colmenar\FutTEquipos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FutTEquiposType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipo')
            ->add('equipo_menu')
            ->add('imagen')
            ->add('orden')
            ->add('patrocinio')
            ->add('web_patrocinio')
            ->add('logo_patrocinio')
            ->add('clasificacion')
            ->add('historico_clasificacion')
            ->add('calendario')
            ->add('ultima_jornada')
            ->add('goleadores')
            ->add('competicion_actual')
            ->add('sexo', ChoiceType::class, [
                'choices' => [
                    'fem' => 'fem',
                    'masc' => 'masc',
                ],
                'attr' => ['class' => 'form-control'], // Agrega clases según sea necesario
                'label' => 'Sexo', // Cambia el texto de la etiqueta si es necesario
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FutTEquipos::class,
        ]);
    }
}

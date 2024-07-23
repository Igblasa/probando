<?php

namespace App\Form;

use App\Entity\colmenar\FutTEmpresas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FutTEmpresasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('empresa')
            ->add('direccion')
            ->add('ciudad')
            ->add('telefono')
            ->add('web')
            ->add('mail')
            ->add('tipo_empresa')
            ->add('imagen')
            ->add('mostrar')
            ->add('orden')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FutTEmpresas::class,
        ]);
    }
}

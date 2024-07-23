<?php

namespace App\Form;

use App\Entity\intranet\IntRoles;
use App\Repository\RolesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesType extends AbstractType
{
    private $rolesRepository;

    public function __construct(RolesRepository $rolesRepository)
    {
        $this->rolesRepository = $rolesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $add = $options['add'];

        if ($add) {
            $builder->add('nombre_role', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);
        } else {
            $roles = $this->rolesRepository->findAll();

            $choices = [];
            foreach ($roles as $role) {
                $choices[$role->getNombreRole()] = $role->getNombreRole();
            }

            $builder->add('nombre_role', ChoiceType::class, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control'],
                'choices' => $choices,
                'required' => true,
            ]);
        }

        $builder->add('descripcion_es', null, [
            'label' => 'Descripción en español',
            'attr' => ['class' => 'form-control'],
            'required' => false,
        ]);

        $builder->add('descripcion_en', null, [
            'label' => 'Descripción en inglés',
            'attr' => ['class' => 'form-control'],
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IntRoles::class,
            'add' => false,
        ]);
    }
}

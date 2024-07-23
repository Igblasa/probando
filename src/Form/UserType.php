<?php

namespace App\Form;

use App\Entity\intranet\IntUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\intranet\IntRoles;

class UserType extends AbstractType
{
    public $parameters;
    
    public function __construct(EntityManagerInterface $entityManager, ContainerBagInterface $parameters) {
        
        $this->entityManager = $entityManager;
        $this->parameters = $parameters;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class, [
                'label'     => 'Roles',
                'multiple'  => true,
                'choices'   => $options['role_choices'],  // Usar los roles pasados desde el controlador
            ])
            ->add('password')
            ->add('is_active')
            ->add('salt');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IntUser::class,
            'role_choices' => [], // Asegúrate de tener esta línea
        ]);
    }
}

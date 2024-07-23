<?php

namespace App\Form;

use App\Entity\colmenar\FutTCronicas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Entity\colmenar\FutTJugadoresNew;

class FutTCronicasType  extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id_equipo = $options['id_equipo'];
        
        $builder
            ->add('textoCronica', TextareaType::class, [
                'label' => 'Texto de la crónica',
                // Remueve o ajusta el atributo 'maxlength'
                // 'attr' => ['maxlength' => 1000], // Por ejemplo, para un máximo de 1000 caracteres
            ])
            ->add('mvp', TextType::class, [
                'label' => 'MVP del Partido',
            ])
            ->add('mvpSemana', TextType::class, [
                'label' => 'MVP de la Semana',
            ]);
                
        $builder->add('imagenes', FileType::class, [
            'label' => 'Introduzca las imágenes',
            'mapped' => false,
            'required' => false,
            'multiple' => true, // Permite seleccionar múltiples archivos. Omite o establece a false si solo se necesita uno.
            'attr' => ['accept' => 'image/*'], // Opcional: Asegura que solo se puedan seleccionar archivos de imagen
        ]);
        
        $builder->add('resultadoLocal', TextType::class, [
            'mapped' => false,
            'required' => false,
            'label' => 'Resultado Local',
        ]);

        $builder->add('resultadoVisitante', TextType::class, [
            'mapped' => false,
            'required' => false,
            'label' => 'Resultado Visitante',
        ]);
        
        for ($i = 1; $i <= 7; $i++) {
            $builder->add('goleador' . $i, TextType::class, [
                'label' => 'Goleador ' . $i,
                'required' => false,
            ]);

            $builder->add('numeroGoles' . $i, ChoiceType::class, [
                'label' => 'Número de Goles ' . $i,
                'required' => false,
                'choices' => [
                    '1' => 1,'2' => 2,'3' => 3,'4' => 4,'5' => 5,'6' => 6,'7' => 7,'8' => 8,'9' => 9,
                ],
            ]);
        }
        if ($this->security->isGranted('ROLE_NACHO')) {
            $builder->add('textoCronicaChatgpt', TextareaType::class, [
                'label' => 'Texto de la crónica pasada por ChatGPT',
                'required' => false,
                // Remueve o ajusta el atributo 'maxlength'
                // 'attr' => ['maxlength' => 1000], // Por ejemplo, para un máximo de 1000 caracteres
            ]);
            $builder->add('usarChatGPT', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Pasar crónicas por ChatGPT',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Sí' => 'si',
                    'No' => 'no',
                ],
                'data' => 'no', // Valor por defecto "No"
                'attr' => ['class' => 'form-check-input'],
            ]);
            $builder->add('publicadoEnRedes', ChoiceType::class, [
                'label' => 'Publicado en redes',
                'choices' => [
                    'No' => 'no',
                    'Sí' => 'si',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);

            $builder->add('enviadoWhatsapp', ChoiceType::class, [
                'label' => 'Enviado por WhatsApp',
                'choices' => [
                    'No' => 'no',
                    'Sí' => 'si',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FutTCronicas::class,
            'id_equipo' => null,
        ]);
    }
}

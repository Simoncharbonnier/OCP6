<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de la figure'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de la figure ne peut être vide.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la description',
                    'cols' => 50,
                    'rows' => 3
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut être vide.'
                    ])
                ]
            ])
            ->add('collection', TextType::class, [
                'label' => 'Groupe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le groupe'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

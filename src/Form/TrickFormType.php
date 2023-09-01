<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                    'rows' => 6
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut être vide.'
                    ])
                ]
            ])
            ->add('collection', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => [
                    'Groupe de la figure' => null,
                    'Group 1' => 1,
                    'Group 2' => 2,
                    'Group 3' => 3
                ]
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

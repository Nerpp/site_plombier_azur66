<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\ServicesType;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $imgConstraints = [
            new ImageConstraint([
                'maxSize' => '4M',
                'mimeTypes' => ['image/jpeg','image/png','image/webp'],
                'mimeTypesMessage' => 'Formats acceptés : JPEG, PNG, WEBP',
            ])
        ];

        $builder
            ->add('src_profil', FileType::class, [
                'label' => 'Photo de profil (JPEG/PNG/WEBP)',
                'mapped' => false,
                'required' => false,
                'constraints' => $imgConstraints,
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo (JPEG/PNG/WEBP)',
                'mapped' => false,
                'required' => false,
                'constraints' => $imgConstraints,
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('sous_titre')
            ->add('telephone')
            ->add('email')
            ->add('adresse')
            ->add('ville')
            ->add('slogan')
            ->add('supp_info')
            ->add('services', CollectionType::class, [
                'entry_type'    => ServicesType::class,
                'entry_options' => ['label' => false],
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'prototype'     => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Admin::class]);
    }
}

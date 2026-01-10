<?php

namespace App\Form;

use App\Entity\PhotoServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;

class PhotoServicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Image (JPEG/PNG/WEBP)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new ImageConstraint([
                        'maxSize' => '4M',
                        'mimeTypes' => ['image/jpeg','image/png','image/webp'],
                        'mimeTypesMessage' => 'Formats acceptés : JPEG, PNG, WEBP',
                    ]),
                ],
                'attr' => ['accept' => 'image/*'],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Description (optionnelle)'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PhotoServices::class]);
    }
}

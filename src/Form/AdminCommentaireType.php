<?php

namespace App\Form;

use App\Entity\AdminCommentaire;
use App\Entity\Source;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author')
            ->add('rating')
            ->add('date')
            ->add('text')
            ->add('source', EntityType::class, [
                'class' => Source::class,
                'choice_label' => 'label',
                'placeholder' => 'Choisir une source',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminCommentaire::class,
        ]);
    }
}

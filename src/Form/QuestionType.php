<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [

                'attr' => [
                    'placeholder' => 'Votre question'
                ]
            ])
            ->add('content', TextareaType::class, [

                'attr' => [
                    'placeholder' => 'Détails de la question'
                ]

            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'form_row button submit'
                ],
                'label' => 'Je pose ma question'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}

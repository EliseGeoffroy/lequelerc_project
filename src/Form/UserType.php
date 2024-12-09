<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $builder->getData();
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'row_attr' => [
                    'class' => 'form-field flex-col-center width-100'
                ]
            ])
            ->add('picture', FileType::class, [
                'constraints' => [
                    new Image(['allowPortrait' => true, 'allowSquare' => true, 'allowLandscape' => false])
                ],
                'label' => 'Charger une photo de profil',
                'row_attr' => [
                    'class' => 'form-field flex-col-center width-100 profile-picture'
                ],
                'required' => !$user->getPicture(),
                'mapped' => false
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur associée à votre compte',
                'row_attr' => [
                    'class' => 'form-field flex-col-center width-100'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'row_attr' => [
                    'class' => 'form-field flex-col-center width-100'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'options' => [
                    'attr' => ['class' => 'password-field'],
                    'row_attr' => [
                        'class' => 'form-field flex-col-center width-100'
                    ],

                ],

                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Rentrez de nouveau votre mot de passe'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

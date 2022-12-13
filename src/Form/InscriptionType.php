<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('pseudo')
            ->add('profilpic', FileType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints'=>new Length([
                    'min'=>2,
                    'max'=>30
                ]),
                'invalid_message' => 'Le mot de passe doit etre identique à la confirmation.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe',
                    'attr'=>['placeholder'=>'Votre mot de passe']
                ],
                'second_options' => ['label' => 'Confirmer le mot de passe',
                    'attr'=>[
                        'placeholder'=>'Confirmer votre mot de passe'
                    ]

                ],
            ])
            ->add('submit', SubmitType::class,[
                'label'=>"S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

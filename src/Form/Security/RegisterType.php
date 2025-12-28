<?php

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Votre pseudo",
                'required' => true,
                'constraints' => [
                    new Length(
                        max: 20,
                        maxMessage: "Votre pseudo peut contenir plus de {{ limit }} caractères."
                    )
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'required' => true,
                'constraints' => [
                    new Length(
                        max: 255,
                        maxMessage: "Votre email ne peut pas contenir plus de {{ limit }} caractères."
                    )
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'constraints' => [
                        new Length(
                            min: 6,
                            max: 40,
                            minMessage: "Votre mot de passe doit contenir au moins {{ limit }} caractères.",
                            maxMessage: "Votre mot de passe ne peut pas contenir plus de {{ limit }} caractères."
                        ),
                        new NotBlank(
                            message: "Veuillez entrer un mot de passe."
                        )
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'data_class' => User::class
        ]);
    }
}

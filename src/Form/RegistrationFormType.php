<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom *',
                'attr' => [
                    'placeholder' => 'Votre prénom',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre prénom.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre prénom doit contenir au minimum {{ limit }} caractères !',
                        'max' => 100,
                        'maxMessage' => 'Votre prénom ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'attr' => [
                    'placeholder' => 'Votre nom',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre nom.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre nom doit contenir au minimum {{ limit }} caractères !',
                        'max' => 150,
                        'maxMessage' => 'Votre nom ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse e-mail.'
                    ]),
                    new Email([
                        'message' => 'Votre adresse e-mail ({{ value }}) est invalide !',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation',
                'first_options' => [
                    'label' => 'Mot de passe *',
                    'attr' => [
                        'placeholder' => 'Votre mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe *',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe',
                    ],
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères !',
                        'maxMessage' => 'Votre mot de passe ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                    new Regex([
                        'pattern' => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/u",
                        'message' => 'Votre mot de passe doit contenir obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer un compte',
                'attr' => [
                    'class' => 'w-100',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

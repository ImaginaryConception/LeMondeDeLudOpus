<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'empty_data' => 'Non renseigné',
                'attr' => [
                    'placeholder' => 'Votre nom',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre nom.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre nom doit contenir au minimum {{ limit }} caractères !',
                        'max' => 150,
                        'maxMessage' => 'Votre nom ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom *',
                'empty_data' => 'Non renseigné',
                'attr' => [
                    'placeholder' => 'Votre prénom',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre prénom.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre prénom doit contenir au minimum {{ limit }} caractères !',
                        'max' => 100,
                        'maxMessage' => 'Votre prénom ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
                'empty_data' => 'Champ vide',
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse e-mail.',
                    ]),
                    new Email([
                        'message' => 'Votre adresse e-mail ({{ value }}) est invalide !',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Confirmer',
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

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactFormType extends AbstractType
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
            ->add('lastname', TextType::class, [
                'label' => 'Nom *',
                'attr' => [
                    'placeholder' => 'Votre nom',
                ],
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
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse e-mail.',
                    ]),
                    new Email([
                        'message' => 'Votre adresse e-mail ({{ value }}) est invalide !',
                    ]),
                ],
            ])
            ->add('object', TextType::class, [
                'label' => 'Objet *',
                'attr' => [
                    'placeholder' => 'Saisissez l\'objet du message',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner l\'objet du message.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'L\'objet du message doit contenir au minimum {{ limit }} caractères !',
                        'max' => 100,
                        'maxMessage' => 'L\'objet du message ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message *',
                'attr' => [
                    'placeholder' => 'Saisissez le contenu du message',
                    'rows' => '5',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre message.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre message doit contenir au minimum {{ limit }} caractères !',
                        'max' => 20000,
                        'maxMessage' => 'Votre message ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'w-100',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

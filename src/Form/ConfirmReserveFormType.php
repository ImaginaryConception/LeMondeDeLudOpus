<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ConfirmReserveFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisissez le contenu des instructions',
                    'rows' => '5',
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Vos instructions doivent contenir au minimum {{ limit }} caractères !',
                        'max' => 20000,
                        'maxMessage' => 'Vos instructions ne doivent pas dépasser {{ limit }} caractères !',
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
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

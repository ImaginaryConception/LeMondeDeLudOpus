<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddCommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre commentaire...',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre commentaire.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le commentaire doit contenir au minimum {{ limit }} caractères !',
                        'max' => 10000,
                        'maxMessage' => 'Le commentaire ne doit pas dépasser {{ limit }} caractères !',
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
            'data_class' => Comment::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

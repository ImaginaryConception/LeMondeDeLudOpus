<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddArticleFormType extends AbstractType
{

    private $allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre *',
                'empty_data' => 'Non renseigné',
                'attr' => [
                    'placeholder' => 'Le titre de l\'article',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner le titre.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit contenir au minimum {{ limit }} caractères !',
                        'max' => 2000,
                        'maxMessage' => 'Le titre ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'attr' => [
                    'placeholder' => 'L\'auteur de l\'article',
                ],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'L\'auteur doit contenir au minimum {{ limit }} caractères !',
                        'max' => 500,
                        'maxMessage' => 'L\'auteur ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('edition', TextType::class, [
                'label' => 'Édition *',
                'empty_data' => 'Non renseigné',
                'attr' => [
                    'placeholder' => 'L\'édition de l\'article',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner l\'édition.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'L\'édition doit contenir au minimum {{ limit }} caractères !',
                        'max' => 1000,
                        'maxMessage' => 'L\'édition ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('genre', TextType::class, [
                'label' => 'Genre *',
                'empty_data' => 'Non renseigné',
                'attr' => [
                    'placeholder' => 'Le genre de l\'article',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner le genre.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le genre doit contenir au minimum {{ limit }} caractères !',
                        'max' => 500,
                        'maxMessage' => 'Le genre ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('other', ChoiceType::class, [
                'label' => 'Format',
                'required' => false,
                'choices'  => [
                    'Aucun' => '',
                    'Poche' => 'Poche',
                    'Dédicacé' => 'dédicacé',
                    'Ancien' => 'ANCIEN',
                ],
                'attr' => [
                    'novalidate' => 'novalidate',
                ],
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix',
                'empty_data' => 'Non renseigné',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Le prix de l\'article',
                ],
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Le prix doit contenir au minimum {{ limit }} caractères !',
                        'max' => 150,
                        'maxMessage' => 'Le prix ne doit pas dépasser {{ limit }} caractères !',
                    ]),
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'required' => false,
                'attr' => [
                    'accept' => implode(', ', $this->allowedMimeTypes),
                    'novalidate' => 'novalidate',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => $this->allowedMimeTypes,
                        'mimeTypesMessage' => 'L\'image doit être du type JPG ou PNG seulement !',
                        'maxSizeMessage' => 'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximum autorisée est {{ limit }}{{ suffix }}',
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
            'data_class' => Article::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

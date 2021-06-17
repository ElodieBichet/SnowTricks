<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => 'Image title'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => "The picture title must have at least {{ limit }} characters",
                        'maxMessage' => "The picture title cannot be longer than {{ limit }} characters"
                    ]),
                    new NotBlank([
                        'message' => "You must define a title"
                    ])
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Image short description'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 140,
                        'minMessage' => "The picture description must have at least {{ limit }} characters",
                        'maxMessage' => "The picture description cannot be longer than {{ limit }} characters"
                    ]),
                    new NotBlank([
                        'message' => "You must write a short description"
                    ])
                ]
            ])
            ->add('filename', FileType::class, [
                'label' => 'Image file (.jpg, .jpeg, .png) - 2Mo max',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the file
                // every time you edit the Picture details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file: jpg, jpeg or png',
                    ])
                ],
            ])
            ->add('trick', EntityType::class, [
                'placeholder' => '-- Choose a trick --',
                'class' => Trick::class,
                'choice_label' => function (Trick $trick) {
                    return ucfirst($trick->getName());
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}

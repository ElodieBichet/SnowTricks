<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', MediaTitleType::class, [
                'data_class' => Video::class,
            ])
            ->add('videoUrl', UrlType::class, [
                'required' => false,
                'label' => 'Video URL from Youtube, Dailymotion or Vimeo',
                'attr' => ['placeholder' => 'https://'],
                'constraints' => [
                    new Regex([
                        'pattern' => '#(youtu)|(dai\.?ly)|(vimeo)#',
                        'message' => 'The video URL must match with Youtube, Dailymotion or Vimeo format'
                    ])
                ]
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
            'data_class' => Video::class,
        ]);
    }
}

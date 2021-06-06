<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\PictureType;
use Doctrine\ORM\EntityRepository;
use App\Repository\PictureRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Trick name']
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['placeholder' => 'Trick description']
            ])
            ->add('trickGroup', EntityType::class, [
                'placeholder' => '-- Choose a group --',
                'class' => Group::class,
                'choice_label' => function (Group $group) {
                    return ucfirst($group->getName());
                }
            ])
            ->add('mainPicture', EntityType::class, [
                'class' => Picture::class,
                'query_builder' => function (PictureRepository $pr) use ($builder) {
                    if ($builder->getData()->getId()) { // only for edit form of an existing trick
                        return $pr->createQueryBuilder('p')
                            ->where('p.trick = ' . $builder->getData()->getId())
                            ->orderBy('p.id', 'ASC');
                    } else { // for new trick form
                        return null;
                    }
                },
                'choice_label' => function (Picture $picture) {
                    return $picture->getTitle();
                },
                'multiple' => false,
                'expanded' => true,
                'choice_attr' => function (Picture $picture) {
                    return ['form' => 'trick', 'data-filename' => $picture->getFilename()];
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

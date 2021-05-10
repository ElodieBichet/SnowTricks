<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Trick name']
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

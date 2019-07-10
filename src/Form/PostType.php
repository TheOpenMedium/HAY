<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class)
            ->add('color', ChoiceType::class, array(
                'choices' => array(
                    '000', '222', '696', '999', 'DDD', 'FFF',
                    'E00', '72C', '008', '099', '0A0', 'F91',
                    'F00', 'D0F', '22F', '6DF', '0F0', 'FD0',
                    'F44', 'F2E', '08F', '0FF', 'BF0', 'EE0',
                    'F05', 'F6F', '0AE', '9FF', '5F9', 'FF0'
                ),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('size', IntegerType::class)
            ->add('user', ChoiceType::class, ['choices' => $options['users']])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'users' => null,
        ]);

        $resolver->setAllowedTypes('users', 'array');
    }
}

<?php

namespace App\Form;

use App\Entity\Line;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'indentation',
                NumberType::class,
                [
                    'html5' => true,
                    'attr' => [
                        'min' => '0',
                        'max' => '100',
                    ]
                ]
            )
            ->add('content');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Line::class,
        ]);
    }
}

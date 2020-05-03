<?php

namespace App\Form;

use App\Entity\LinesTask;
use App\Entity\Line;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinesTaskType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('lines', CollectionType::class, [
      'entry_type' => LineType::class,
      'entry_options' => ['label' => false],
    ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => LinesTask::class,
    ]);
  }
}

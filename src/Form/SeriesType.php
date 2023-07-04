<?php
namespace App\Form;

use App\Entity\Author;
use App\Entity\Series;
use App\Entity\Publisher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name');
        $builder->add('description', NumberType::class, ["required" => false]);
        $builder->add('author', EntityType::class, [
            'class' => Author::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => "name"
        ])
        ->add('save', SubmitType::class, ['label' => 'Create Book', 'attr' => ['class' => 'MyClass']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Series::class,
        ]);
    }
}
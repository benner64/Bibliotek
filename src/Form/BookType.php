<?php
namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Publisher;
use App\Entity\Series;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name')
        ->add('pages', NumberType::class, ["required" => false])
        ->add("publisher", EntityType::class, [
            'class' => Publisher::class,
            'choice_label' => "name",
            "required" => false
        ])
        ->add("series", EntityType::class, [
            'class' => Series::class,
            'choice_label' => "name",
            "required" => false
        ])
        ->add("coverImageFile", FileType::class, [
            'label' => 'Cover image',

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/x-png',
                        'image/png'
                    ],
                    'mimeTypesMessage' => 'Please upload a valid cover image for the book',
                ])
            ],
        ])
        ->add('genres', EntityType::class, [
            'class' => Genre::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => "name",
        ])
        ->add('author', EntityType::class, [
            'class' => Author::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => "name"
        ])
        ->add("publishYear", DateTimeType::class, [
            "widget" => "single_text",
            "required" => false

        ])
        ->add('save', SubmitType::class, ['label' => ($options['CreateOrUpdate'] ? 'Create' : 'Update') . " Book", 'attr' => ['class' => 'btn-success']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'CreateOrUpdate' => false
        ]);

        $resolver->setAllowedTypes('CreateOrUpdate', 'bool');
    }
}
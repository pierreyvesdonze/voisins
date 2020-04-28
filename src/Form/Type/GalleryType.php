<?php

namespace App\Form\Type;

use App\Entity\Event;
use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class GalleryType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'title',
            TextType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Titre de la photo (optionnel)'
                ],
            ]
        );

        $builder->add('brochure', FileType::class, [

            'label'         =>false,
            'mapped'        => false,
            'required'      => false,
            'multiple'      => true,
            'attr'  => [
                'placeholder' => 'Photo',
            ],
            'constraints'   => [
                new File([])
            ],
        ]);

        $builder->add(
            'upload',
            SubmitType::class,
            [
                "label" => "Ok",
                'attr' => [
                    'class' => 'button',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}

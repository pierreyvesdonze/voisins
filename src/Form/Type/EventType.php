<?php

namespace App\Form\Type;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'title',
            TextType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Intitulé'
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                "choices" => [
                    "Apero"      => "apero",
                    "Boulange"   => "boulange",
                    "Bouffe"     => "bouffe",
                    "Courses"    => "courses",
                    "Service"    => "service",
                    "Autre"      => "autre"
                ],
                'expanded' => false,
                'constraints' => [
                    new NotBlank(),
                ]
            ]
        );

        $builder->add('date', DateTimeType::class, [
            'required' => false,
            'empty_data' => null,
            'widget' => 'single_text',
            'attr' => [
                'html5' => false,
                'placeholder' => 'Date',
                'class' => 'form-control datetimepicker-input',
                'data-target' => '#datetimepicker1',
                'autocomplete' => 'off',
            ],
        
        ]);

        $builder->add(
            'lieu',
            TextType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Lieu'
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ]
        );

        $builder->add(
            'text',
            TextareaType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Ajouter une description'
                ]
            ]
        );

        $builder->add('brochure', FileType::class, [

            'label'         => 'Ajoute un ticket de caisse (après bien sûr)',
            'mapped'        => false,
            'required'      => false,
            'constraints'   => [
                new File([])
            ],
        ]);

        $builder->add(
            'save',
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
            'data_class' => Event::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}

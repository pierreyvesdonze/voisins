<?php

namespace App\Form\Type;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'title',
            TextType::class,
            [
                "label" => "Intitulé de l'événement"
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                "choices" => [
                    "Invitation" => "Invitation",
                    "Courses"    => "Courses",
                    "Service"    => "Service"
                ],
                'expanded' => true,
                'required' => false,
            ]
        );

        $builder->add('date', DateTimeType::class, [
            'placeholder' => [
                'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                'hour' => 'Hour', 'minute' => 'Minute'
            ]
        ]);

        $builder->add(
            'lieu',
            TextType::class,
            [
                "label" => "Lieu"
            ]
        );

        $builder->add(
            'text',
            TextareaType::class,
            [
                "label" => "Ajouter une description"
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                "label" => "Ajouter"
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

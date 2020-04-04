<?php

namespace App\Form\Type;

use App\Entity\Participate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipateType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
       
        $builder->add(
            'save',
            SubmitType::class,
            [
                "label" => "Oui"
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participate::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}

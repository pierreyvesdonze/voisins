<?php

namespace App\Form\Type;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'comment',
            TextareaType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Ajoute ton commentaire',
                    'class' => 'comment-textarea'
                ]
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                "label" => "Créer",
                'attr' => [
                    'class' => 'button',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}

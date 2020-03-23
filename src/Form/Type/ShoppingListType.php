<?php

namespace App\Form\Type;

use App\Entity\Ingredient;
use App\Entity\ShoppingList;
use App\Entity\ShoppingListIngredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'title',
            TextType::class,
            [
                "label" => "Titre de ta liste"
            ]
        );

        $builder->add(
            'description',
            TextareaType::class,
            [
                "label" => "Ta liste"
            ]
        );

       
        $builder->add(
            'save',
            SubmitType::class,
            [
                "label" => "Enregistrer"
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShoppingList::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}

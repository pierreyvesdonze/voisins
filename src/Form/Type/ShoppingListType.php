<?php

namespace App\Form\Type;

use App\Entity\Ingredient;
use App\Entity\ShoppingList;
use App\Entity\ShoppingListIngredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {

        $builder->add('ingredient', CollectionType::class, [
            'label' => false,
            'entry_type' => ShoppingListIngredient::class,
            'entry_options' => array('label' => false),
        ]);
       
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
            'data_class' => ShoppingListIngredient::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}

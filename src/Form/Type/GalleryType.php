<?php

namespace App\Form\Type;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title')
        // On ajoute le champ "photos" dans le formulaire
        // Il n'est pas lié à la base de données (mapped à false)
        ->add('photos', FileType::class,[
            'label' => false,
            'multiple' => true,
            'mapped' => false,
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
